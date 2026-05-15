<?php

use App\Constants\RouteNames;
use App\Http\Controllers\DriverCompany\ChatController;
use App\Http\Controllers\DriverCompany\DriverCompanyController;
use App\Http\Controllers\General\ComplaintController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Users\Auth\AuthController;
use App\Http\Controllers\Users\Finance\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Driver Company API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Driver Companies API routes for driver companies in the system
|
*/

// No Auth Needed
Route::middleware([])->group(function () {
    
});

//Auth Needed
Route::group(['middleware' => ['auth:api', "is_user", 'token.access_api', 'user.active', 'user.verified']], function () {
    
    //transactions
    Route::prefix("transactions")->controller(WalletController::class)->group(function () {
        Route::get("/wallet", "getMyWallet");
        Route::post("/transfer", "transfer");
        Route::get("/sent", "getSentTransactions");
        Route::get("/received", "getReceivedTransactions");
    });

    //Products
    Route::prefix("products")->controller(ProductController::class)->group(function () {
        Route::get("/get", "index");
    });


    //Orders
    Route::prefix("orders")->controller(OrderController::class)->group(function () {
        Route::get("/", "get");
        Route::post('add' , 'addToCart');
        Route::delete('/remove/{id}' , 'removeFromCart');
    });
    
});
