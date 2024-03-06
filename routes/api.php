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

Route::group(['prefix' => 'user','middleware' => 'withauth'], function() {
    Route::post('/order', [\App\Http\Controllers\orderController::class,'placeOrder']);
    Route::post('/verify-order', [\App\Http\Controllers\orderController::class,'verifyOrder']);
    Route::get('/orders', [\App\Http\Controllers\orderController::class,'getMyOrders']);
    Route::post('/upgrade-package', [\App\Http\Controllers\orderController::class,'upgradePackage']);
    Route::post('/verify-upgrade', [\App\Http\Controllers\orderController::class,'verifyUpgrade']);

});
