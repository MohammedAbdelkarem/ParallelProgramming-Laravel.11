<?php

namespace App\Http\Middleware;

use App\Constants\ExceptionMessages;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Gate::allows("superAdmin"))
            return $next($request);
        return failure(
            ExceptionMessages::MSG_NOT_AUTHORIZED,
            403
        );
    }
}
