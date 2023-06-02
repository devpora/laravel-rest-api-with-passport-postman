<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware(['cors', 'json.response'])->namespace('App\Http\Controllers')->group(function () {
    // Routes for unauthenticated user
    Route::get('today', 'CalendarController@today');
    Route::group(['prefix' => 'auth'], function () {
        //Login
        Route::post('login','AuthController@login');
        //Register
        Route::post('register', 'AuthController@register');
    });
    Route::group(['prefix' => 'oauth'], function () {
        Route::post('login', 'OAuthController@login');
    });

    // Routes for authenticated user
    Route::middleware('auth:api')->group(function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('logout', 'AuthController@logout');
        });

        Route::get('today_auth', 'CalendarController@todayAuth');
        Route::get('me', 'UserController@me');
    });
});
