<?php

namespace App\Http\Middleware\User;

use App\Constants\ExceptionMessages;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();
        $profile = $user->profile;

        if (
            $profile->banned_until
            && Carbon::now()->lt(Carbon::parse($profile->banned_until))
        )
            return failure(
                ExceptionMessages::MSG_BANNED_ACCOUNT,
                400,
                ['time' => Carbon::parse($profile->banned_until)->translatedFormat('Y-m-d g:i A')],
            );
        return $next($request);
    }
}
