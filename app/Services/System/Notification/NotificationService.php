<?php

namespace App\Services\System\Notification;

use App\Enums\Notifications\NotificationScreens;
use App\Enums\Notifications\NotificationTypes;
use App\Jobs\SendNotificationsJob;
use App\Models\System\Notification\Notification;
use App\Models\System\Role\Role;
use App\Models\User;
use App\Models\Users\Profile\UserDevice;
use App\Services\MainService;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class NotificationService.
 */
class NotificationService extends MainService
{
    public function index($per_page)
    {
        return Notification::query()
            ->whereHas("creator", function ($query) {
                $query->whereNotIn("role_id", [3, 4]);
            })
            ->withCount(['receivers', 'views'])
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
    }

    public function preStore()
    {
        $data = [
            "types" => NotificationTypes::values(),
            "roles" =>  Role::orderBy("id")->get(['id', 'name']),
        ];
        return $data;
    }

    public function storePrivate($validatedData)
    {
        //ids_list is users
        /**
         * ids_list have higher priority than trageted users thats why there is this condition
         */

        DB::beginTransaction();
        $ids_list = [];
        //Get By ids_list
        if (!empty($validatedData["ids_list"])) {
            $ids_list = $validatedData["ids_list"];
        } else
            //Get By role id
            $ids_list = User::whereIn('role_id', $validatedData["target_users"])
                ->pluck('id')
                ->toArray();

        //Get Device Tokens
        $tokens_list = UserDevice::query()
            ->whereIn('user_id', $ids_list)
            ->whereHas("user", function ($query) {
                $query->whereNull("deactive_at")->where('active_notifications', true);
            })
            ->pluck('notification_token')
            ->toArray();

        //Create Notification
        $notification = $this->createNotification(
            title: json_encode(["message" => $validatedData["title"], "attributes" => []]),
            body: json_encode(["message" => $validatedData["body"], "attributes" => []]),
            type: $validatedData["type"],
            // createdBy: auth()->id(),
            // page: $this->getNotificationPage($validatedData["type"])
        );

        //Store receivers
        $notification->receivers()->attach($ids_list, [
            "is_read" => false,
        ]);

        DB::commit();

        //Dispatch Job To Send Notification
        dispatch(new SendNotificationsJob(
            tokens: $tokens_list,
            notification: $notification,
            shouldTranslate: false,
        ));
    }

    // public function storePublic($validatedData)
    // {
    //     //Create Notiification
    //     $notification = $this->createNotification(
    //         title: json_encode(["message" => $validatedData["title"], "attributes" => []]),
    //         body: json_encode(["message" => $validatedData["body"], "attributes" => []]),
    //         type: NotificationTypes::PUBLIC->value,
    //         createdBy: auth()->id(),
    //         public: true,
    //     );

    //     //Dispatch Job To Send Notification
    //     dispatch(new SendNotificationsJob(
    //         tokens: UserDevice::query()
    //             ->whereIn('user_id', User::query()->whereNull("deactive_at")->where('active_notifications', true)->pluck('id')->toArray())
    //             ->pluck('notification_token')
    //             ->toArray(),
    //         notification: $notification,
    //         shouldTranslate: false,
    //     ));
    // }

    public function show($id)
    {
        $user = auth()->user();

        return Notification::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('receivers', function ($q) use ($user) {
                    $q->where('user_id', $user?->id);
                });
            })
            ->with('receivers', function ($query) use ($user) {
                $query->where('user_id', $user?->id);
            })
            ->findOrFail($id);
    }

    public function showAdmin($id)
    {
        /**
         * Notification should be either:
         * Created by an admin
         * Or this admin is one of the recivers
         * Or public
         */
        return Notification::query()
            ->where("id", $id)
            ->where(function ($query) {
                $query->whereHas('creator', function ($q) {
                    $q->whereNot('role_id', 3);
                })
                    ->orWhereHas('receivers', function ($q) {
                        $q->where('user_id', auth()->id());
                    })->orWhere('is_public', true);
            })
            ->with('receivers', function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->withCount('receivers')
            ->withCount('views')
            ->firstOrFail();
    }

    public function getUnreadNotificationsCount()
    {
        $user = auth()->user();

        return Notification::query()
            ->whereHas('receivers', function ($q) use ($user) {
                $q->where('user_id', $user?->id)->where('is_read', false);
            })
            ->count();
    }

    public function getMyNotifications($per_page)
    {
        $user = auth()->user();

        // Get Notifications with("recivers") relation where recivers are this user only And get public notifications
        $notifications = Notification::query()
            ->when(auth()->user()->isAdmin(), function ($q) {
                $q->where('created_at' , null);
            })
            ->whereHas('receivers', function ($q) use ($user) {
                $q->where('user_id', $user?->id);
            })
            ->orWhere('is_public', true)
            ->with('receivers', function ($query) use ($user) {
                $query->where('user_id', $user?->id);
            })
            ->orderBy('created_at', 'Desc')
            ->paginate($per_page);

        //Set Notification as readed

        // Collect the notification IDs
        $notificationIds = $notifications->pluck('id')->toArray();

        // Set Notification as read
        if ($user)
            $user->notifications()
                ->wherePivotIn('notification_id', $notificationIds)
                ->wherePivot('is_read', 0)
                ->updateExistingPivot($notificationIds, [
                    'is_read' => 1,
                ]);

        // if(auth()->user()->isAdmin())
        //     return [];

        return $notifications;
    }

    public function destroy($id)
    {
        $notification = Notification::query()
            ->whereNot("type", "Account")
            ->whereHas('creator', function ($q) {
                $q->whereNot('role_id', 3);
            })
            ->findOrFail($id);

        $notification->delete();
    }

    // protected function getNotificationPage(string $type) // TODO Replace with real path
    // {
    //     $page = NotificationScreens::HOME->value;
    //     switch ($type) { // Values are same to what used in NotificationTypes enum
    //         case NotificationTypes::PUBLIC->value:
    //             $page = NotificationScreens::HOME->value;
    //             break;
    //         case NotificationTypes::ACCOUNT->value:
    //             $page = NotificationScreens::PROFILE_SCREEN->value;
    //             break;
    //         case NotificationTypes::PRODUCT->value:
    //             $page = NotificationScreens::PRODUCTS->value;
    //             break;
    //         default:
    //             break;
    //     }
    //     return $page;
    // }

    public function getEligibleUserIds($notification_management, $string)
    {
        return User::query()
            ->where('role_id', 4)
            ->whereNull('deactive_at')
            ->where('active_notifications', true)
            ->when($notification_management, function ($q) use ($string) {
                $q->whereHas('notification_management', function ($q2) use ($string) {
                    $q2->where($string . '_notification', 1);
                });
            })
            ->pluck('id')
            ->toArray();
    }

    
    public function getNotificationTokens(array $userIds)
    {
        return UserDevice::query()
            ->whereIn('user_id', $userIds)
            ->pluck('notification_token')
            ->toArray();
    }
}
