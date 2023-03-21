<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/404', function () {
    abort(404, 'This page not found');
});

Route::get('/exception', function () {
    throw new Exception('Test exception');
});

Route::get('/postman_env', function () {
    $file = public_path() . '/postman/LaravelRestApiWithPassport.postman_environment.json';
    if (file_exists($file)) {
        return Response::download($file, 'LaravelRestApiWithPassport.postman_environment.json');
    }
});

Route::get('/postman_col', function () {
    $file = public_path() . '/postman/LaravelRestApiWithPassport.postman_collection.json';
    if (file_exists($file)) {
        return Response::download($file, 'LaravelRestApiWithPassport.postman_collection.json');
    }
});
