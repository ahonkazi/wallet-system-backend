<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Route::group(['prefix' => 'auth'], function() {
    Route::post('/register', [\App\Http\Controllers\authController::class,'register']);
    Route::post('/login', [\App\Http\Controllers\authController::class,'login']);
});

Route::group(['prefix' => 'admin','middleware' => 'withauth'], function() {
    Route::post('/package', [\App\Http\Controllers\packageController::class,'createPackage']);
    
});