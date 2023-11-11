<?php

use App\Http\Controllers\TiktokController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// User Login
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->get('/tiktok/campaigns', [TiktokController::class,'getCampaigns']);
Route::middleware('auth:sanctum')->get('/tiktok/ads_groups', [TiktokController::class,'getAdsGroups']);

