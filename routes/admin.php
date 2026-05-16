<?php

use App\Constants\RouteNames;
use App\Http\Controllers\Administration\AdminHomeController;
use App\Http\Controllers\Administration\Auth\AuthController;
use App\Http\Controllers\Administration\Finance\WalletController;
use App\Http\Controllers\Administration\Log\BanLogController;
use App\Http\Controllers\Administration\Profile\AdminProfileController;
use App\Http\Controllers\Administration\Profile\UserProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\System\Notification\NotificationController;
use App\Http\Controllers\System\SystemSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admins API routes for admins in the system
|
*/

// No Auth Needed
Route::middleware([])->group(function () {
    Route::controller(AuthController::class)->middleware('bots')->group(function () {
        Route::post("/login", "login")->name('login');
    });
    Route::controller(AdminHomeController::class)->group(function () {
        Route::post('processing/{value}', 'processing')
            ->withoutMiddleware(['broadcasting.encoder'])
            ->name('admin.processing.run')
            ->middleware('aop.performance:admin.processing.run');
    });
});

//Auth Needed
Route::group(['middleware' => ['auth:api', "is_admin", 'token.access_api', 'user.active', 'user.verified']], function () {

    // Auth
    Route::controller(AuthController::class)->group(function () {
        Route::get("/active-session", "activeSessions");
        Route::post("/logout-session", "logoutSessions");
        Route::post("/logout", "logout");
        Route::get("/logout-all", "logoutAll");
        Route::get("/refresh", "refresh")->withoutMiddleware('token.access_api')->withoutMiddleware('token.access_refresh');
    });

    // //Home
    Route::controller(AdminHomeController::class)->group(function () {
        Route::get("/home", "home");
        Route::get("/overview", "overview");
    });

    //Profiles
    //Admins
    Route::prefix('profile')->controller(AdminProfileController::class)->group(function () {
        //Profile Settings
        Route::get("/login-history/{id?}", "loginHistory")->name(RouteNames::LOGIN_HISTORY_List);
        Route::post("/lang", "changeLang");
        Route::get("/notifications-status", "changeNotificationState");
        Route::get("/sugs", "adminSugs");
        Route::get("/list", "index")->name(RouteNames::ADMINS_LIST);
        Route::get("/{id}", "show");
                                                                   //Only super admin can access this routes
        Route::middleware(['is_super_admin'])->group(function () { //TODO:NEED CHECK FOR DYNAMIC AND POLICIES
            Route::post("/", "store");
            Route::put("/{id}", "update");
            Route::put("/update-image/{id}", "updateProfileImage");
            Route::get("/deactivate/{id}", "deactivateAccount");
        });
    });

    //Users
    Route::prefix("users")->group(function () {
        Route::controller(UserProfileController::class)->group(function () {
            Route::get("/sugs", "userSugs");
            Route::get("/list", "index")->name(RouteNames::USERS_LIST);
            Route::get("/profile/{id}", "show");
            Route::post("/restore", "restore");
            Route::get("/change-is-preffered", "changeIsPrefferedStatus");
        });

        Route::prefix("ban")->controller(BanLogController::class)->group(function () {
            Route::post("/", "ban");
            Route::post("/remove", "unBan");
        });
    });

    //Logs
    Route::prefix("logs")->group(function () {
        Route::prefix("bans-log")->controller(BanLogController::class)->group(function () {
            Route::get("/", "index")->name(RouteNames::BANLOG_LIST);
            Route::get("/{id}", "show");
        });
    });

    //System Info
    Route::prefix("system")->group(function () {

        Route::apiResource('/settings', SystemSettingController::class);
    });

    //Notifications
    Route::prefix("notifications")->controller(NotificationController::class)->group(function () {
        Route::get("/list", "index")->name(RouteNames::NOTIFICATIONS_LIST);
        Route::get("/", "getMyNotifications")->name(RouteNames::MY_NOTIFICATIONS_LIST);
        Route::get("/pre-store", "preStore");
        Route::post("/", "storePublic");
        Route::post("/private", "storePrivate");
        Route::get("/{id}", "showAdmin");
        Route::delete("/{id}", "destroy");
    });

    //transactions
    Route::prefix("transactions")->controller(WalletController::class)->group(function () {
        Route::get("/wallet", "getMyWallet");
        Route::post("/transfer", "transfer");
        Route::get("/sent", "getSentTransactions");
        Route::get("/received", "getReceivedTransactions");
    });

    //Products
    Route::apiResource("/products", ProductController::class);

    //Orders

    Route::prefix("orders")->controller(OrderController::class)->group(function () {
        Route::get("/", "get");

        Route::patch("/{orderId}/change-status", "changeOrderStatus")
            ->name('admin.orders.change-status')
            ->middleware('aop.performance:admin.orders.change-status');
    });

});
