<?php

namespace App\Jobs;

use App\Models\System\Notification\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\NotificationHelper;

class SendNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, NotificationHelper;

    public $tokens, $notification, $additionalData, $shouldTranslate;

    /**
     * Create a new job instance.
     *
     * @param array $tokens the list of tokens to send the notification to
     * @param \App\Models\System\Notification\Notification $notification the notification to send
     * @param array $additionalData the additional data to send with the notification
     * @param bool $shouldTranslate Check if the notification should be from translation files
     */
    public function __construct($tokens = [], $notification, $additionalData = [], $shouldTranslate = true)
    {
        $this->tokens = $tokens;
        $this->notification = $notification;
        $this->additionalData = $additionalData;
        $this->shouldTranslate = $shouldTranslate;
    }

    public function handle(): void
    {
        $this->additionalData += [
            'id' => $this->notification->requested_id,
        ];

        $this->sendNotification(
            tokens: $this->tokens,
            title: $this->notification->title,
            body: $this->notification->body,
            additionalData: $this->additionalData,
            local: 'ar',
            shouldTranslate: $this->shouldTranslate
        );
    }
}
