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

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [\App\Http\Controllers\authController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\authController::class, 'login']);

});


Route::group(['prefix' => 'public'], function () {
    Route::get('/packages', [\App\Http\Controllers\packageController::class, 'getPackages']);


});

Route::group(['prefix' => 'admin', 'middleware' => 'withauth'], function () {
    Route::post('/package', [\App\Http\Controllers\packageController::class, 'createPackage']);
    Route::post('/create-role', [\App\Http\Controllers\roleController::class, 'createRole']);
    Route::post('/create-permission', [\App\Http\Controllers\roleController::class, 'createPermission']);
    Route::post('/assign-role', [\App\Http\Controllers\roleController::class, 'assignRole']);
    Route::post('/assign-permission', [\App\Http\Controllers\roleController::class, 'assignPermission']);

});

Route::group(['prefix' => 'user', 'middleware' => 'withauth'], function () {
    Route::patch('/basic-information', [\App\Http\Controllers\userController::class, 'editUserInformation']);
    Route::post('/logout', [\App\Http\Controllers\authController::class, 'logout']);
    Route::get('/basic-information', [\App\Http\Controllers\userController::class, 'editUserInformation']);
    Route::get('/settings', [\App\Http\Controllers\userController::class, 'getUserSettings']);
    Route::post('/order', [\App\Http\Controllers\orderController::class, 'placeOrder']);
    Route::post('/verify-order', [\App\Http\Controllers\orderController::class, 'verifyOrder']);
    Route::get('/orders', [\App\Http\Controllers\orderController::class, 'getMyOrders']);
    Route::post('/upgrade-package', [\App\Http\Controllers\orderController::class, 'upgradePackage']);
    Route::post('/verify-upgrade', [\App\Http\Controllers\orderController::class, 'verifyUpgrade']);

    Route::group(['prefix' => 'wallet'], function () {
        //bank
        Route::patch('/bank-accounts/{id}', [\App\Http\Controllers\BankAccountController::class, 'editBankAccount']);
        Route::post('/bank-accounts', [\App\Http\Controllers\BankAccountController::class, 'addBankAccount']);
        Route::get('/bank-accounts', [\App\Http\Controllers\BankAccountController::class, 'getBankAccount']);

        //card
        Route::patch('/card-accounts/{id}', [\App\Http\Controllers\CardController::class, 'editCardAccount']);
        Route::post('/card-accounts', [\App\Http\Controllers\CardController::class, 'addCardAccount']);
        Route::get('/card-accounts', [\App\Http\Controllers\CardController::class, 'getCardInformation']);

    });
    Route::group(['prefix' => 'identity','middleware'=>'IdentityMiddleware'], function () {
        //    nid
        Route::patch('/nid-information/{id}', [\App\Http\Controllers\NidController::class, 'editNidInformation']);
        Route::post('/nid-information', [\App\Http\Controllers\NidController::class, 'addNidInformation']);
     //    passport
        Route::patch('/passport-information/{id}', [\App\Http\Controllers\PassportController::class, 'editPassportInformation']);
        Route::post('/passport-information', [\App\Http\Controllers\PassportController::class, 'addPassportInformation']);

    });


});
