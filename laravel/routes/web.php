<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/privacy-policy', function () {
    return view('privacy');
});

Route::get('/login', function () {
    return view('login');
});

Route::post('/main/checklogin', [\App\Http\Controllers\AuthController::class,'checklogin']);
Route::get('main/successlogin', [\App\Http\Controllers\AuthController::class,'successlogin']);
