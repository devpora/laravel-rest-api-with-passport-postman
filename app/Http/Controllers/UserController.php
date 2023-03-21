<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function me()
    {
        $user = Auth::user();

        return response([
            'name' => $user->name,
            'email' => $user->email
        ], 200);
    }

}
