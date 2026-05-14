<?php

namespace App\Services\Administration\Log;

use App\Constants\NotificationMessages;
use App\Enums\Notifications\NotificationScreens;
use App\Enums\Notifications\NotificationTypes;
use App\Services\MainService;
use Illuminate\Support\Facades\DB;
use App\Models\Administration\Log\BanLog;
use App\Models\User;
use Carbon\Carbon;

class BanLogService extends MainService
{
    public function index($per_page, $search = null, $order = "desc", $id = null)
    {
        if ($id) $search = ""; //Because I'm getting bans for user by his id

        //Check for order input
        in_array($order, ["desc", "asc"]) ? $order : $order = "desc";

        //Get Bans
        $bans = BanLog::query()
            ->when($id, function ($query) use ($id) {
                $query->where('banned_id', $id);
            })
            ->when($search, function ($query) use ($search) {
                $query->WhereHas("bannedUser", function ($q) use ($search) {
                    $q->withTrashed()
                        ->searchName($search);
                });
            })
            ->with(["banningUser" => function ($query) {
                $query->withTrashed();
            }])
            ->with(["unbanningUser" => function ($query) {
                $query->withTrashed();
            }])
            ->with(["bannedUser" => function ($query) {
                $query->withTrashed();
            }])
            ->orderBy("created_at", $order)
            ->paginate($per_page);

        return $bans;
    }

    public function show($id)
    {
        return findByIdOrFail(BanLog::class, $id, asQuery: true)
            ->with(["banningUser" => function ($query) {
                $query->withTrashed();
            }])
            ->with(["unbanningUser" => function ($query) {
                $query->withTrashed();
            }])
            ->with(["bannedUser" => function ($query) {
                $query->withTrashed();
            }])->first();
    }

    public function ban($valdatedData)
    {
        //Get The User
        $user = User::withTrashed()
            ->where("id", $valdatedData["user_id"])->with('profile')->first();
        $profile = $user->profile;

        //Check if user already banned then this is ban update else it's first time ban (Effect Notifications)
        $noBanToBan = false;
        if (!$profile->banned_until) $noBanToBan = true;

        $lastBanOnUser = BanLog::query()
            ->where([
                "banned_id" => $user->id,
                "is_active" => true,
            ])
            ->where('banned_until', '>', Carbon::now()->format("Y-m-d H:i"))
            ->orderBy("created_at", "desc")
            ->first();

        //Create Ban On User
        $profile->banned_until = Carbon::parse($valdatedData["banned_until"])->format("Y-m-d H:i");
        $profile->save();

        //Create BanLog
        BanLog::updateOrCreate(
            [
                "id"        => $lastBanOnUser?->id,
                "banned_id" => $user->id,
            ],
            [
                "is_active"     => true,
                "banned_by_id"  => auth()->id(),
                "reason"        => $valdatedData["reason"],
                "banned_until"  => Carbon::parse($profile->banned_until)->format("Y-m-d H:i"),
            ]
        );

        $this->sendDirectNotification(
                 $user->id,
             $this->notificationMessage(NotificationMessages::BAN_TITLE),
             $this->notificationMessage(NotificationMessages::BAN_BODY, ["bannedUntil" => $profile->banned_until, "reason" => $valdatedData["reason"]]),
             NotificationTypes::AUTH->value,
                'ar',
                false,
                "",
                [],
                true,
                [],
                true
            );
    }

    public function unBan($validatedData)
    {
        //Get User
        $user = User::query()
            ->withTrashed()
            ->where(['id' => $validatedData['user_id'], "role_id" => 3])
            ->withWhereHas("profile", function ($query) {
                $query->whereNotNull("banned_until");
            })
            ->firstOrFail();

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
            "unban_reason"   => $validatedData["unban_reason"],
            "unbanned_by_id" => auth()->id(),
        ]);

        $this->sendDirectNotification(
                 $user->id,
              $this->notificationMessage(NotificationMessages::UNBAN_TITLE ),
         $this->notificationMessage(NotificationMessages::UNBAN_BODY),
             NotificationTypes::AUTH->value,
                'ar',
                false,
                "",
                [],
                true,
                [],
                true
            );
    }
}