<?php

namespace App\Console\Commands\Users;

use App\Constants\NotificationMessages;
use App\Enums\Notifications\NotificationScreens;
use App\Enums\Notifications\NotificationTypes;
use App\Enums\Product\ProductHiddenCases;
use App\Models\Administration\Log\BanLog;
use App\Models\User;
use App\Models\Users\Product\Product;
use App\Models\Users\Profile\UserProfile;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BanRemoverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ban-remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description will remove ended bans and re-show products wich is hidden cause of ban';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::withWhereHas("profile", function ($query) {
            $query->whereNotNull("banned_until")
                ->where('banned_until', '<', Carbon::now()->format("Y-m-d H:i:s"));
        })->get();

        DB::beginTransaction();
        foreach ($users as $user) {
            $lastBanOnUser = BanLog::query()
                ->where([
                    "banned_id" => $user->id,
                    "is_active" => true,
                ])
                ->where('banned_until', '>', Carbon::now()->format("Y-m-d H:i"))
                ->orderBy("created_at", "desc")
                ->first();

            //Unban the user
            $user->profile->banned_until = null;
            $user->profile->save();

            //Update BanLog
            $lastBanOnUser->update([
                "is_active"      => false,
                "unban_reason"   => 'auto',
                "unbanned_by_id" => null,
            ]);

            $this->sendDirectNotification(
                targeted_user_id: $user->id,
                title: $this->notificationMessage(NotificationMessages::UNBAN_TITLE),
                body: $this->notificationMessage(NotificationMessages::UNBAN_BODY),
                type: NotificationTypes::AUTH->value,
                // createdBy: null,
                // page: NotificationScreens::PROFILE_SCREEN->value,
                local: $user->language,
            );
            usleep(500000); // 0.5sec
        }
        DB::commit();
    }
}