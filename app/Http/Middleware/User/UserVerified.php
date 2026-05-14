<?php

namespace App\Http\Middleware\User;

use App\Constants\ExceptionMessages;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->account_verified_at)
            return $next($request);
        return failure(
            ExceptionMessages::MSG_ACCEESS_DENIED,
            401
        );
    }
}
