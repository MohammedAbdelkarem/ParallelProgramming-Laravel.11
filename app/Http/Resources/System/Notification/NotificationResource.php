<?php

namespace App\Http\Resources\System\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TimeFormatter;

class NotificationResource extends JsonResource
{
    use TimeFormatter;
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->getTitle(),
            "body" => $this->getBody(),
            "date" => $this->getHumanReadableTime($this->created_at, -48, -60),
            'is_read' => $this->receivers->isEmpty() ? true : (bool)$this->receivers[0]->pivot->is_read,
            "type" => $this->type,
            // "page" => $this->page,
            "clickable"  => (bool) $this->clickable,
            // "is_public"  => (bool) $this->is_public,
            "extra_data" => $this->extra_data ? json_decode($this->extra_data) : [],
            "requested_id" => $this->requested_id ?? "",
        ];
    }

    public function getTitle()
    {
        $title = json_decode($this->title);
        return __("notifications.{$title->message}", (array)$title->attributes);
    }

    public function getBody()
    {
        $body = json_decode($this->body);
        return __("notifications.{$body->message}", (array)$body->attributes);
    }
}