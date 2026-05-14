<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class CheckPerPage
{
    public function handle(Request $request, Closure $next): Response
    {
        $perPage = config('_custom.per_page_default_value');

        /**
         * remove per_page if it's numeric
         */
        if ($request->has('per_page') && !is_numeric($request->per_page))
            $request->query->remove('per_page');
        //If frontend want to have all items without pagination
        //Route name should include 'selectable'
        if (
            Str::contains(request()->route()->getName(), 'selectable') &&
            (
                !$request->has('per_page')
                || ($request->has('per_page') && $request->per_page <= 0)
            )
        ) {
            $request->merge(['per_page' => -1]);
            return $next($request);
        }

        /**
         * If request should include pagination.
         * Route name should include 'list'.
         * Notice that if request has ('selectable' and 'list') in the route name => will selectable will have the upper priority.
         */
        if (
            Str::contains(request()->route()->getName(), 'listtttt') &&
            (
                !$request->has('per_page')
                || $request->per_page <= 0
                || $request->per_page > config('_custom.max_per_page_value')
            )
        )
            $request->merge(['per_page' => $perPage]);
        return $next($request);
    }
}