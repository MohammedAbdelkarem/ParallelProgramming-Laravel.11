<?php

namespace App\Http\Middleware\Tokens;

use App\Constants\ExceptionMessages;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckApiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::payload()->get('api_access'))
            return $next($request);
        return failure(
            message: ExceptionMessages::MSG_UNAUTHENTICATED,
            statusCode: 401,
        );
    }
}
