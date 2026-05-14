<?php

namespace App\Http\Middleware;

use App\Constants\ExceptionMessages;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Gate::allows("user"))
            return $next($request);
        return failure(
            ExceptionMessages::MSG_NOT_AUTHORIZED,
            403
        );
    }
}
