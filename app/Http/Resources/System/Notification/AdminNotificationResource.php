<?php

namespace App\Http\Resources\System\Notification;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TimeFormatter;

class AdminNotificationResource extends JsonResource
{
    use TimeFormatter;
    public function toArray(Request $request): array
    {
        $viewCount = 0;
        $this["receivers_count"] != 0 ?
            $viewCount = (string)((float) round($this["views_count"] / $this["receivers_count"], 3) * 100) : false;
        if ($this->is_public) {
            $viewCount = "NAN";
            $this["views_count"] = "NAN";
            $this["receivers_count"] = User::count();
        }

        return [
            "id"    => (int)$this->id,
            "title" => $this->getTitle(),
            "body"  => $this->getBody(),
            "date"  => $this->getHumanReadableTime($this->created_at, -48, -60),
            "type_trans"    => __("notifications.{$this->type}"),
            "type"          => $this->type,
            "is_public"     => (bool) $this->is_public,
            "clickable"     => (bool) $this->clickable,
            "requested_id"  => $this->requested_id ?? "",
            "page"      => $this->page,
            "views"     => $this->views_count ?? 0,
            "receivers" => $this->receivers_count ?? 0,
            "viewsRate" => "{$viewCount} %",
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
