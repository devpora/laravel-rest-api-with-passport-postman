<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OAuthController extends Controller
{
    public $provider;
    public $oAuthCode;

    public const OAUTH_BASIC_CALLBACK_URL = 'http://localhost:3000/oauth/callback/';

    public const PROVIDER_GOOGLE_NAME = 'google';
    public const PROVIDER_GOOGLE_GET_TOKEN_URL = 'https://oauth2.googleapis.com/token';
    public const PROVIDER_GOOGLE_CHECK_TOKEN_URL = 'https://www.googleapis.com/oauth2/v3/tokeninfo';

    public const PROVIDER_GITHUB_NAME = 'github';
    public const PROVIDER_GITHUB_GET_TOKEN_URL = 'https://github.com/login/oauth/access_token';
    public const PROVIDER_GITHUB_CHECK_TOKEN_URL = 'https://api.github.com/user';
    public const PROVIDER_GITHUB_GET_EMAIL_URL = 'https://api.github.com/user/emails';

    public const PROVIDER_GITLAB_NAME = 'gitlab';
    public const PROVIDER_GITLAB_GET_TOKEN_URL = 'https://gitlab.com/oauth/token';
    public const PROVIDER_GITLAB_CHECK_TOKEN_URL = 'https://gitlab.com/api/v4/user';

    public function login(Request $request)
    {
        // Validation Request
        $request->validate([
            'oAuthCode' => 'required',
            'provider' => 'required|in:' .
                self::PROVIDER_GOOGLE_NAME . ',' .
                self::PROVIDER_GITHUB_NAME . ',' .
                self::PROVIDER_GITLAB_NAME,
        ]);

        $this->provider = $request->input('provider');
        $this->oAuthCode = $request->input('oAuthCode');

        // Check login
        $result = $this->oAuthLogin();

        if(!$result['status']){
            return response(['message' => isset($result['message']) ? $result['message'] : 'Login Failed'], 406);
        }

        // Login user or Register if not exist
        $user = User::firstOrCreate([
            'email' => $result['email'],
        ], [
            'name' => $result['name'],
            'password' => Hash::make(Str::random(24)),
        ]);

        // Generate token with passport
        $tokenResult = $user->createToken('Laravel Password Grant Client');

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'userData' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
    }


    private function oAuthLogin(){
        if($this->provider === self::PROVIDER_GOOGLE_NAME){
            $result = $this->oAuthLoginGoogle();
        }elseif($this->provider === self::PROVIDER_GITHUB_NAME){
            $result = $this->oAuthLoginGitHub();
        }elseif($this->provider === self::PROVIDER_GITLAB_NAME){
            $result = $this->oAuthLoginGitLab();
        }else{
            return ['status' => false];
        }

        return $result;
    }



    private function oAuthLoginGoogle(){
        $result = [];
        $result['status'] = false;
        $result['message'] = '';

        try{
            // Get accessToken
            $url = $this->getOAuthTokenUrl();
            $data = $this->getOAuthData();

            $response = Http::withHeaders([
                'Content-Type'=> 'application/json',
                'Accept'=> 'application/json',
            ])->asForm()->post($url, $data);

            $responseData = $response->json();
            if (!$response->successful()) {
                $result['message'] = isset($responseData['error']) ? $responseData['error'] : 'Failed login';

                return $result;
            }

            $data = $response->json();

            // Login OK, get user data with authorization_token
            $oAuthAccessToken = $data['access_token'];

            // Check Token in provider
            $providerUrl = $this->getOAuthCheckTokenUrl();

            $response = Http::post($providerUrl, [
                'access_token' => $oAuthAccessToken,
            ]);

            if(!$response->successful()){
                return response(['message' => $response->body()], 406);
            }

            $userData = $response->json();

            // Create username from email
            $dotPosition = strpos($userData['email'], '.');

            if ($dotPosition !== false) {
                $firstName = ucfirst(substr($userData['email'], 0, $dotPosition));
                $lastName = ucfirst(substr($userData['email'], $dotPosition + 1, strpos($userData['email'], '@') - $dotPosition - 1));
                $username = $firstName . ' ' . $lastName;
            } else {
                $atPosition = strpos($userData['email'], '@');
                $username = ucfirst(substr($userData['email'], 0, $atPosition));
            }

            $result['name'] = $username;
            $result['email'] = $userData['email'];
            $result['status'] = true;

            return $result;

        }catch (\Exception $e){
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    private function oAuthLoginGitHub(){
        $result = [];
        $result['status'] = false;
        $result['message'] = '';

        try{
            // Get accessToken
            $url = $this->getOAuthTokenUrl();
            $data = $this->getOAuthData();

            $response = Http::withHeaders([
                'Content-Type'=> 'application/json',
                'Accept'=> 'application/json',
            ])->asForm()->post($url, $data);

            $responseData = $response->json();
            if($response->status() !== ResponseAlias::HTTP_OK){
                $result['message'] = isset($responseData['error']) ? $responseData['error'] : $response->body();

                return $result;
            }

            if(isset($responseData['error'])){
                $result['message'] = isset($responseData['error_description']) ? $responseData['error_description'] : $responseData['error'];

                return $result;
            }

            if(isset($responseData['access_token'])){
                // GET USER DATA
                $userDataUrl = $this->getOAuthCheckTokenUrl();
                $response = Http::withHeaders([
                    'Content-Type'=> 'application/json',
                    'Authorization'=> 'Bearer ' . $responseData['access_token'],
                    'Accept'=> 'application/json',
                ])->get($userDataUrl);

                if($response->status() !== ResponseAlias::HTTP_OK){
                    $result['message'] = $response->body();

                    return $result;
                }

                $responseUserData = $response->json();
                $result['name'] = $responseUserData['login'];

                // GET USER EMAIL
                $userDataUrl = $this->getUserEmailFromProvider();
                $response = Http::withHeaders([
                    'Content-Type'=> 'application/json',
                    'Authorization'=> 'Bearer ' . $responseData['access_token'],
                    'Accept'=> 'application/json',
                ])->get($userDataUrl);

                if($response->status() !== ResponseAlias::HTTP_OK){
                    $result['message'] = $response->body();

                    return $result;
                }
                $responseUserEmail = $response->json();
                $primaryEmailObj = array_filter($responseUserEmail, function($emailObj) {
                    return $emailObj['primary'];
                });

                $primaryEmail = reset($primaryEmailObj)['email'];

                $result['email'] = $primaryEmail;
                $result['status'] = true;

                return $result;

            }
        }catch (\Exception $e){
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    private function oAuthLoginGitLab(){
        $result = [];
        $result['status'] = false;
        $result['message'] = '';

        try{
            // Get accessToken
            $url = $this->getOAuthTokenUrl();
            $data = $this->getOAuthData();

            $response = Http::withHeaders([
                'Content-Type'=> 'application/json',
                'Accept'=> 'application/json',
            ])->asForm()->post($url, $data);

            $responseData = $response->json();
            if($response->status() !== ResponseAlias::HTTP_OK){
                $result['message'] = isset($responseData['error']) ? $responseData['error'] : $response->body();

                return $result;
            }

            if(isset($responseData['error'])){
                $result['message'] = isset($responseData['error_description']) ? $responseData['error_description'] : $responseData['error'];

                return $result;
            }

            if(isset($responseData['access_token'])){
                // GET USER DATA
                $userDataUrl = $this->getOAuthCheckTokenUrl();
                $response = Http::withHeaders([
                    'Content-Type'=> 'application/json',
                    'Authorization'=> 'Bearer ' . $responseData['access_token'],
                    'Accept'=> 'application/json',
                ])->get($userDataUrl);

                if($response->status() !== ResponseAlias::HTTP_OK){
                    $result['message'] = $response->body();

                    return $result;
                }

                $responseUserData = $response->json();
                $result['name'] = $responseUserData['name'];
                $result['email'] = $responseUserData['email'];
                $result['status'] = true;

                return $result;

            }
        }catch (\Exception $e){
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    public function getOAuthTokenUrl(){
        $url = null;
        if($this->provider === self::PROVIDER_GOOGLE_NAME){
            $url = self::PROVIDER_GOOGLE_GET_TOKEN_URL;
        }elseif($this->provider === self::PROVIDER_GITHUB_NAME){
            $url = self::PROVIDER_GITHUB_GET_TOKEN_URL;
        }elseif($this->provider === self::PROVIDER_GITLAB_NAME){
            $url = self::PROVIDER_GITLAB_GET_TOKEN_URL;
        }

        return $url;
    }

    public function getOAuthCheckTokenUrl(){
        $url = null;
        if($this->provider === self::PROVIDER_GOOGLE_NAME){
            $url = self::PROVIDER_GOOGLE_CHECK_TOKEN_URL;
        }elseif($this->provider === self::PROVIDER_GITHUB_NAME){
            $url = self::PROVIDER_GITHUB_CHECK_TOKEN_URL;
        }elseif($this->provider === self::PROVIDER_GITLAB_NAME){
            $url = self::PROVIDER_GITLAB_CHECK_TOKEN_URL;
        }

        return $url;
    }

    public function getUserEmailFromProvider(){
        $url = null;
        if($this->provider === self::PROVIDER_GITHUB_NAME){
            $url = self::PROVIDER_GITHUB_GET_EMAIL_URL;
        }

        return $url;
    }

    public function getOAuthData(){
        $data = null;
        if($this->provider === self::PROVIDER_GOOGLE_NAME){
            $data = [
                "code"=> $this->oAuthCode,
                "client_id"=> env('OAUTH_GOOGLE_CLIENT_ID'),
                "client_secret"=> env('OAUTH_GOOGLE_CLIENT_SECRET'),
                "redirect_uri"=> self::OAUTH_BASIC_CALLBACK_URL . $this->provider,
                "grant_type"=> "authorization_code",
            ];
        }elseif($this->provider === self::PROVIDER_GITHUB_NAME){
            $data = [
                "code"=> $this->oAuthCode,
                "client_id"=> env('OAUTH_GITHUB_CLIENT_ID'),
                "client_secret"=> env('OAUTH_GITHUB_CLIENT_SECRET'),
            ];
        }elseif($this->provider === self::PROVIDER_GITLAB_NAME){
            $data = [
                "code"=> $this->oAuthCode,
                "client_id"=> env('OAUTH_GITLAB_CLIENT_ID'),
                "client_secret"=> env('OAUTH_GITLAB_CLIENT_SECRET'),
                "redirect_uri"=> self::OAUTH_BASIC_CALLBACK_URL . $this->provider,
                "grant_type"=> "authorization_code",
                "code_verifier"=> env("OAUTH_GITLAB_CODE_VERIFIER"),
            ];
        }
        return $data;
    }

}
