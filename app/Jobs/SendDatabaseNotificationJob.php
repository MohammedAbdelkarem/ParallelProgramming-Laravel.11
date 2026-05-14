<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\NotificationHelper;

class SendDatabaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, NotificationHelper;

    public $targeted_user_id , $title , $body , $type , $local , $clickable , $requestedID , $extraData , $shouldCreate , $additionalData , $shouldTranslate;
    /**
     * Create a new job instance.
     */
    public function __construct(
         $targeted_user_id,
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
    )
    {
        $this->targeted_user_id = $targeted_user_id;
        $this->title = $title;
        $this->body = $body;
        $this->type = $type;
        $this->local = $local;
        $this->clickable = $clickable;
        $this->requestedID = $requestedID;
        $this->extraData = $extraData;
        $this->shouldCreate = $shouldCreate;
        $this->additionalData = $additionalData;
        $this->shouldTranslate = $shouldTranslate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->sendDirectNotification(
            $this->targeted_user_id,
            $this->title,
            $this->body,
            $this->type,
            $this->local,
            $this->clickable,
            $this->requestedID,
            $this->extraData,
            $this->shouldCreate,
            $this->additionalData,
            $this->shouldTranslate,
        );
    }
}
