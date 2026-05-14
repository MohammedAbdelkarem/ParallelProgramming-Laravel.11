<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLang
{
    public function handle(Request $request, Closure $next)
    {
        $allowed = ['ar', 'en', 'ur', 'zh'];

        // Read header
        $lang = $request->header('x-lang');

        // Set locale if valid, otherwise default to 'en'
        app()->setLocale(in_array($lang, $allowed) ? $lang : 'en');

        return $next($request);
    }
    
}
