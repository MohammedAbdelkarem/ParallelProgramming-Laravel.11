<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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
        Route::post("/transfer", "transfer")
            ->name('user.transactions.transfer')
            ->middleware('aop.performance:user.transactions.transfer');

        Route::get("/sent", "getSentTransactions");
        Route::get("/received", "getReceivedTransactions");
    });

    //Products
    Route::prefix("products")->controller(ProductController::class)->group(function () {
        Route::get("/get", "index")
            ->name('user.products.index')
            ->middleware('aop.performance:user.products.index');
    });

    //Orders
    Route::prefix("orders")->controller(OrderController::class)->group(function () {
        Route::get("/", "get")
            ->name('user.orders.index')
            ->middleware('aop.performance:user.orders.index');

        Route::post('add', 'addToCart')
            ->name('user.orders.add')
            ->middleware('aop.performance:user.orders.add');

        Route::delete('/remove/{id}', 'removeFromCart')->name('user.orders.remove')
            ->middleware('aop.performance:user.orders.remove');
    });

});
