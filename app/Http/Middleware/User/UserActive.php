<?php

namespace App\Http\Middleware\User;

use App\Constants\ExceptionMessages;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user()->deactive_at)
            return $next($request);
        return failure(
            ExceptionMessages::MSG_UNACTIVE_ACCOUNT,
            401
        );
    }
}
