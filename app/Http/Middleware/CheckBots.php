<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckBots
{
    public function handle(Request $request, Closure $next)
    {
        $fieldName = 'full_name';

        $validator = Validator::make($request->only($fieldName), [
            $fieldName => ['present'],
        ]);
        if ($validator->fails())
            if (!$request->hasHeader($fieldName))
                return success(
                    message: "ok",
                    statusCode: config("_custom.honeybot_SC")
                );
        if (
            ($request->exists($fieldName) && is_null($request[$fieldName]))
            || ($request->hasHeader($fieldName) && empty($request->header($fieldName)))
        ) {
            return $next($request);
        }
        return success(
            message: "ok",
            statusCode: config("_custom.honeybot_SC")
        );
    }
}