<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware(['api', 'xss', 'json', 'per_page', 'db_transaction', 'throttle:api'])
                ->prefix('api/')
                ->group(base_path('routes/api.php'));

            Route::middleware(['api', 'xss', 'json', 'per_page', 'db_transaction', 'throttle:api'])
                ->prefix('api/admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['api', 'xss', 'json', 'per_page', 'db_transaction', 'throttle:api'])
                ->prefix('api/user')
                ->group(base_path('routes/user.php'));
        }
    )
    //added
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => ['api', 'token.access_api', 'json']] 
    )
    ///

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'xss'           => \App\Http\Middleware\XSSProtection::class,
            'bots'          => \App\Http\Middleware\CheckBots::class,
            'lang'          => \App\Http\Middleware\CheckLang::class,
            'json'          => \App\Http\Middleware\ForceJson::class,
            'per_page'      => \App\Http\Middleware\CheckPerPage::class,
            'db_transaction' => \App\Http\Middleware\DatabaseTransaction::class,
            //Users Access
            'is_user'        => \App\Http\Middleware\CheckUser::class,
            'is_admin'       => \App\Http\Middleware\CheckAdmin::class,
            'is_super_admin' => \App\Http\Middleware\CheckSuperAdmin::class,
            
            //User Account Middleware
            'user.active'            => \App\Http\Middleware\User\UserActive::class,
            'user.banned'            => \App\Http\Middleware\User\UserBanned::class,
            'user.complete'          => \App\Http\Middleware\User\UserCompleted::class,
            'user.verified'          => \App\Http\Middleware\User\UserVerified::class,
            //Tokens Middleware
            'token.access_api'       => \App\Http\Middleware\Tokens\CheckApiAccess::class,
            'token.access_refresh'   => \App\Http\Middleware\Tokens\CheckRefreshAccess::class,
            'token.access_otp'       => \App\Http\Middleware\Tokens\CheckOTPAccess::class,
        ]);
        $middleware->append(\App\Http\Middleware\CheckLang::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('broadcasting/*')) {
                return response()->json([
                    'error' => 'Broadcasting Auth Failed',
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    })
    ->withSingletons([
        \Illuminate\Contracts\Debug\ExceptionHandler::class => \App\Exceptions\Handler::class,
    ])->create();