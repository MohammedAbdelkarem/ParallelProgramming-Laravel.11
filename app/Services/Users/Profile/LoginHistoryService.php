<?php

namespace App\Services\Users\Profile;

use App\Constants\NotificationMessages;
use App\Enums\Notifications\NotificationScreens;
use App\Enums\Notifications\NotificationTypes;
use App\Models\Users\Profile\LoginHistory;
use App\Services\MainService;

/**
 * Class LoginHistoryService.
 */
class LoginHistoryService extends MainService
{
    public function index($per_page, $id = null)
    {
        return LoginHistory::query()->where("user_id", $id ?? auth()->id())
            ->orderByDesc("created_at")
            ->paginate($per_page);
    }

    public function store(int $user_id, string $device, $ip , $device_id = null)
    {
        $loginHistory = LoginHistory::create([
            "ip_address" => $ip ? $ip->ip : "0.0.0.0",
            "country_code" => $ip ? $ip->countryCode : "N/A",
            "country" => $ip ? $ip->countryName : "N/A",
            "city" => $ip ? $ip->cityName : "N/A",
            "user_id" => $user_id,
            "device_name" => $device,
            "device_id" => $device_id,
        ]);
        $this->sendLoginNotification($device, $ip, $user_id);
        return $loginHistory;
    }

    private function sendLoginNotification($device, $ip, $user_id): bool
    {
        $city = $ip ? $ip->cityName : "N/A";
        $country = $ip ? $ip->countryName : "N/A";

        // $this->sendDirectNotification(
        //     targeted_user_id: $user_id,
        //     title: $this->notificationMessage(NotificationMessages::LOGIN_TITLE),
        //     body: $this->notificationMessage(NotificationMessages::LOGIN_BODY, ["device" => $device, "location" => $country . " / " . $city]),
        //     type: NotificationTypes::ACCOUNT->value,
        //     createdBy: $user_id,
        //     page: NotificationScreens::PROFILE_SCREEN->value,
        // );

        $this->sendDirectNotification(
            $user_id,
        $this->notificationMessage(NotificationMessages::LOGIN_TITLE),
        $this->notificationMessage(NotificationMessages::LOGIN_BODY, ["device" => $device, "location" => $country . " / " . $city]),
            NotificationTypes::AUTH->value,
            'ar',
            false,
            "",
            
                [
                'city' => $city,
                'country' => $country
                ]
            ,
            true,
            [],
            true
        );
    
    
        return true;
    }
}