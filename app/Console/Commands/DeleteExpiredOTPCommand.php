<?php

namespace App\Console\Commands;

use App\Models\JWTPersonalTokens;
use App\Models\OTP;
use App\Models\Users\Profile\NumberUpdate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredOTPCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will delete the expired otps to clean the DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        OTP::query()
            ->where('expire_at', '<', Carbon::now()->format('Y-m-d H:i:s'))
            ->delete();

        NumberUpdate::query()
            ->where('expire_at', '<', Carbon::now()->format('Y-m-d H:i:s'))
            ->delete();
    }
}