<?php

namespace App\Console\Commands;

use App\Models\JWTPersonalTokens;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will delete the expired users tokens to clean the DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        JWTPersonalTokens::query()
            ->where('expire_at', '<', Carbon::now()->format('Y-m-d H:i:s'))
            ->delete();
    }
}
