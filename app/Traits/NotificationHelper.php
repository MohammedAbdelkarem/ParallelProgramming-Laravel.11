<?php

namespace App\Traits;

use App\Models\System\Notification\Notification;
use App\Models\Users\Profile\UserDevice;
use Illuminate\Support\Facades\DB;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;

trait NotificationHelper
{
    /**
     * Sends a notification to a user directly, without queuing.
     * If $shouldCreate is true, creates a new notification and saves it to the database.
     * If $shouldCreate is false, does not create a new notification and only sends the notification to the user.
     *
     * @param int $targeted_user_id The id of the user to send the notification to
     * @param string $title The title of the notification
     * @param string $body The body of the notification
     * @param string $type The type of the notification
     * @param mixed $created_by The user who created the notification
     * @param string $local The notification local language
     * @param bool $public Whether the notification should be public
     * @param string $page The page the notification should redirect to
     * @param bool $clickable Whether the notification should be clickable
     * @param string $requestedID The requested id of the notification for clickable case
     * @param array $extraData Any extra data to be stored with the notification
     * @param bool $shouldCreate Whether to create a new notification in the database
     * @param array $additionalData Any additional data to be sent with the notification
     * @return Notification|bool The created notification if $shouldCreate is true, or true if $shouldCreate is false
     * @param bool $shouldTranslate Check if the notification should be from translation files
     */
    protected function sendDirectNotification(
        int $targeted_user_id,
        string $title,
        string $body,
        string $type,
        string $local = 'ar',
        bool $clickable = false,
        string $requestedID = "",
        array $extraData ,
        bool $shouldCreate = true,
        array $additionalData = [],
        bool $shouldTranslate = true,
    ) {
        // dd()
        DB::beginTransaction();
        if (!$clickable) $requestedID = "";
        if ($shouldCreate) {
            $notification = $this->createNotification(
                $title,
                $body,
                $type,
                $clickable,
                $requestedID,
                json_encode($extraData)
            );
            $notification->receivers()->attach($targeted_user_id);
        }

        $additionalData += [
            'id' => $notification->requested_id,
        ];

        $this->sendNotification(
            $this->getTokens($targeted_user_id),
            $title,
            $body,
            $additionalData,
            $local,
            $shouldTranslate,
        );
        DB::commit();

        if ($shouldCreate)
            return $notification;
        return true;
    }

    public function getTokens($targeted_user_id): array
    {
        $tokens = UserDevice::query()->where('user_id', $targeted_user_id)
            ->whereHas("user", function ($query) {
                $query->where('active_notifications', true);
            })
            ->pluck('notification_token')
            ->toArray();

        if (request()->route()->getActionMethod() == 'userVerify') {
            // Filter out the token from the result
            $tokens = array_filter($tokens, function ($token) {
                return $token !== request()->notification_token;
            });
        }

        // Optional: Reindex array if needed
        $tokens = array_values($tokens);
        return $tokens;
    }

    public function notificationMessage($message, $attributes = [])
    {
        return json_encode(["message" => $message, "attributes" => $attributes]);
    }

    /**
     * Create a new notification.
     *
     * @param string $title
     * @param string $body
     * @param string $type
     * @param mixed $createdBy
     * @param bool $is_public
     * @param string $page
     * @param bool $clickable
     * @param string $requestedID
     * @param array $extraData
     * @return Notification
     */
    protected function createNotification(string $title, string $body, string $type, bool $clickable = false, string $requestedID = "", $extraData = null , bool $is_public = false)
    {
        DB::beginTransaction();
        $notification = Notification::create([
            "title"         => $title,
            "body"          => $body,
            "type"          => $type,
            "clickable"     => $clickable,
            "requested_id"  => $requestedID,
            "extra_data"    => $extraData,
            "is_public"    => $is_public,
        ]);
        DB::commit();
        return $notification;
    }

    /**
     * Send a notification to given tokens.
     *
     * @param array $tokens Tokens to send the notification to.
     * @param string $title The title of the notification.
     * @param string $body The body of the notification.
     * @param string $page The page to open when the notification is clicked.
     * @param array $additionalData Additional data to send with the notification.
     * @param string $loacl The lang of the notification to be send in
     * @param bool $shouldTranslate Check if the notification should be from translation files
     *
     * @return void
     */
    public function sendNotification(array $tokens = [], string $title, string $body, $additionalData = [], string $local = 'en', bool $shouldTranslate = true)
    {
        try {
            $div = 500; //between 1 -> 1000
            $start = 0;
            $size = sizeof($tokens);

            // Path to the service account key JSON file
            $serviceAccountPath = config('services.fcm.credentialsPath');

            // Initialize the Firebase Factory with the service account
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);

            // Create the Messaging instance
            $messaging = $factory->createMessaging();

            $title = json_decode($title);
            $body  = json_decode($body);

            if ($shouldTranslate)
                $message = [
                    'title'     => __("notifications." . $title->message, (array)$title->attributes, $local),
                    'body'      => __("notifications." . $body->message, (array)$body->attributes, $local),
                ];
            else
                $message = [
                    'title'     =>  $title->message,
                    'body'      =>  $body->message,
                ];

            for ($i = 0; $i < ceil($size / $div); $i++) {
                $tokensSubArray = array_slice($tokens, $start, $div);

                if (!empty($tokensSubArray)) {
                    $finalmessage = CloudMessage::new()
                        ->withNotification($message);

                    if (!empty($additionalData))
                        $finalmessage = $finalmessage->withData($additionalData);
                    $sendReport = $messaging->sendMulticast($finalmessage, $tokensSubArray);
                }
                //Cooldown time to not be banned by the service
                sleep(1);
                $start += $div;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
