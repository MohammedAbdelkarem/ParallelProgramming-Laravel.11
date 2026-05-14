<?php

use App\Constants\RouteNames;
use App\Http\Controllers\Administration\AdminHomeController;
use App\Http\Controllers\Administration\Auth\AuthController;
use App\Http\Controllers\Administration\ComplaintController;
use App\Http\Controllers\Administration\CustomClearenceCompanyController;
use App\Http\Controllers\Administration\CustomerController;
use App\Http\Controllers\Administration\DriverCompanyController;
use App\Http\Controllers\Administration\DriverController;
use App\Http\Controllers\Administration\Finance\WalletController;
use App\Http\Controllers\Administration\ZATCAController;
use App\Http\Controllers\Administration\Log\BanLogController;
use App\Http\Controllers\Administration\OrderController;
use App\Http\Controllers\Administration\PrivacyPolicy\PrivacyPolicyController;
use App\Http\Controllers\Administration\Profile\AdminProfileController;
use App\Http\Controllers\Administration\Profile\UserProfileController;
use App\Http\Controllers\Administration\SellPoint\SellPointController;
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
        Route::post('processing/{value}' , 'processing')->withoutMiddleware(['broadcasting.encoder']);
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

    //Privacy Policy
    Route::prefix("privace-policy")->controller(PrivacyPolicyController::class)->group(function () {
        Route::get("/", "get");
        Route::post("/create", "create");
        Route::post("/update/{id}", "update");
    });

    //transactions
    Route::prefix("transactions")->controller(WalletController::class)->group(function () {
        Route::get("/wallet", "getMyWallet");
        Route::get("/actor-profile/{id}", "getActorProfile");
        Route::post("/transfer", "transfer");
        Route::patch("/archive/{settlement_id}", "archive");
        Route::get("/settlements", "getSettlements");
        Route::get("/settlements/{id}", "showSettlement");
        Route::get("/sent", "getSentTransactions");
        Route::get("/received", "getReceivedTransactions");
        Route::post("/update-fee-settings/{wallet_id}", "updateWithdrawalFeeSettings");
    });

    //Drivers
    Route::prefix("drivers")->controller(DriverController::class)->group(function () {
        Route::get("/by-status", "getDriversByStatus")->name(RouteNames::DRIVER_BY_STATUS);
        Route::get("/show/{id}", "showDriver")->name(RouteNames::DRIVER_SHOW_FOR_ADMIN);
        Route::post("/approve-saudi-join-request", "approveSaudiJoinRequest")->name(RouteNames::APPROVE_SAUDI_JOIN_REQUEST);
        Route::post("/approve-saudi-employee-join-request", "approveSaudiEmployeeJoinRequest")->name(RouteNames::APPROVE_SAUDI_EMPLOYEE_JOIN_REQUEST);
        Route::post("/approve-non-saudi-employee-join-request", "approveNonSaudiEmployeeJoinRequest")->name(RouteNames::APPROVE_NON_SAUDI_EMPLOYEE_JOIN_REQUEST);
        Route::post("/reject-driver/{id}", "rejectDriver");
    });

    //Driver Companies
    Route::prefix("driver-companies")->controller(DriverCompanyController::class)->group(function () {
        Route::get("/by-status", "getDriverCompaniesByStatus")->name(RouteNames::DRIVER_COMPANY_BY_STATUS);
        Route::get("/show/{id}", "showDriverCompany")->name(RouteNames::DRIVER_COMPANY_SHOW_FOR_ADMIN);
        Route::post("/approve-driver-company-request", "approveDriverCompany")->name(RouteNames::APPROVE_DRIVER_COMPANY_REQUEST);
        Route::post("/reject-driver-company/{id}", "rejectDriverCompany");
    });

    //Driver Companies
    Route::prefix("custom-clearence-company")->controller(CustomClearenceCompanyController::class)->group(function () {
        Route::get("/by-status", "getCustomClearenceCopmaniesByStatus")->name(RouteNames::CUSTOM_CLEARENCE_COMPANY_BY_STATUS);
        Route::get("/show/{id}", "showCustomClearenceCompany")->name(RouteNames::CUSTOM_CLEARENCE_COMPANY_SHOW_FOR_ADMIN);
        Route::post("/approve-custom-clearence-company-request", "approveCustomClearenceCompany")->name(RouteNames::APPROVE_CUSTOM_CLEARENCE_COMPNAY_REQUEST);
        Route::post("/reject-custom-clearence-company/{id}", "rejectCustomClearenceCompany");
    });

    // ZATCA
    Route::prefix("zatca")->controller(ZATCAController::class)->group(function () {
        Route::post("/generate", "generate");
        Route::get("/active", "getActive");
    });

    //Customers
    Route::prefix("customers")->controller(CustomerController::class)->group(function () {
        Route::get("/by-status", "getCustomersByStatus")->name(RouteNames::CUSTOMER_BY_STATUS);
        Route::get("/show/{id}", "showCustomer")->name(RouteNames::CUSTOMER_SHOW_FOR_ADMIN);
        Route::post("/approve-single-customer-request", "approveSingleCustomer")->name(RouteNames::APPROVE_SINGLE_CUSTOMER_REQUEST);
        Route::post("/approve-company-customer-request", "approveCompanyCustomer")->name(RouteNames::APPROVE_COMPANY_CUSTOMER_REQUEST);
        Route::post("/approve-governorate-customer-request", "approveGovernorateCustomer")->name(RouteNames::APPROVE_GOVERNORATE_CUSTOMER_REQUEST);
        Route::post("/reject-customer/{id}", "rejectCustomer");
    });

    //Complaints
    Route::prefix("complaints")->controller(ComplaintController::class)->group(function () {
        Route::get("/index", "index");
        Route::get("/show/{id}" , "show");
        Route::patch("/archive/{id}", "archive");
    });

    //Order
    Route::prefix("orders")->controller(OrderController::class)->group(function () {
        Route::get("/get", "get");
        Route::get("/show/{id}", "show");
    });
    
});