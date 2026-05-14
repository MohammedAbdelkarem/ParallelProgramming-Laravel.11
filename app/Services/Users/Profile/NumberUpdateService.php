<?php

namespace App\Services\Users\Profile;

use App\Constants\ApiMessages;
use App\Models\Users\Profile\NumberUpdate;
use App\Traits\UniqueKey;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\RateLimiter;
use App\Constants\ExceptionMessages;
use App\Jobs\SendSMSOTPJob;
use Carbon\Carbon;

/**
 * Class NumberUpdateService.
 */
class NumberUpdateService
{
    use UniqueKey;

    public function updateNumber($validated)
    {
        $otp = $this->generateUniqeNumericKey(NumberUpdate::class,  "otp", min: 110001, max: 990000);
        NumberUpdate::updateOrCreate([
            "user_id"       => auth()->id(),
        ], [
            "otp"           => $otp,
            "expire_at"     => now()->addMinutes(config("_custom.otp_expire_in")),
            "phone_number"  => $validated["phone_number"],
        ]);

        //TODO dispatch Queue
        SendSMSOTPJob::dispatch($validated["phone_number"], $otp);
    }

    public function verifyNumber($validatedData)
    {
        $user = auth()->user();

        if (
            $this->OTPAttempts() && ($otp = NumberUpdate::query()
                ->where(["otp" => $validatedData["otp"], "user_id" => $user->id])
                ->where("expire_at", ">", Carbon::now()->format("Y-m-d H:i:s"))
                ->first())
        ) {
            $user->phone_number = $otp->phone_number;
            $user->save();

            $otp->delete(); // Delete the otp because it's used
            return null;
        }

        throw new ApiException(null, trans(ApiMessages::MSG_INVALID_OTP_CODE), 400);
    }

    public function OTPAttempts($hit = true)
    {
        $key = "otp_attempts:user-update-number" . auth()->id();
        if (RateLimiter::tooManyAttempts($key, config("_custom.otp_max_attempts"))) {
            throw new ApiException(null, trans(ExceptionMessages::MSG_TOO_MANY_ATTEMPTS, ["decay_minutes" => round(config("_custom.otp_decay_minutes") / 60, 1)]), 400);
        }
        $hit ? RateLimiter::hit($key, config("_custom.otp_decay_minutes")) : false;
        return true;
    }
}