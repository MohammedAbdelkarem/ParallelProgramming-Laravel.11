<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        $this->observersRegister();
        $this->apiRateLimiter();
        $this->gates();
        $this->policies();
    }

    public function observersRegister()
    {
        User::observe(UserObserver::class);
    }

    public function apiRateLimiter()
    {
        RateLimiter::for('api', function (Request $request) {
            if (Auth::user()) {
                return Limit::perMinute(30)->by(Auth::id());
            } else
                return Limit::perMinute(20)->by($request->ip());
        });
    }

    public function gates()
    {
        //Gates If Needed

        Gate::define('superAdmin', function ($user): bool {
            return $user->isSuperAdmin();
        });
        Gate::define('admin', function ($user): bool {
            return $user->isAdmin();
        });
        Gate::define('user', function ($user): bool {
            return $user->isRegularUser();
        });
    }

    public function policies()
    {
        //Policies Here
    }
}