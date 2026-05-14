<?php

namespace App\Http\Middleware\User;

use App\Constants\ExceptionMessages;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->name)
            return $next($request);
        return failure(
            ExceptionMessages::MSG_INCOMPLETE_ACCOUNT,
            config('_custom.complete_profile_SC')
        );
    }
}
