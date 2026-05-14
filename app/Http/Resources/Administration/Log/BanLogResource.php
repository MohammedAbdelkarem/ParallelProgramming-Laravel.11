<?php

namespace App\Http\Resources\Administration\Log;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\Media\MediaResource;

class BanLogResource extends BaseJsonResource
{
    protected function resourceArray($request)
    {
        $banned       = $this->bannedUser;
        $bannedBy     = $this->banningUser;
        $unbannedBy   = $this->unbanningUser;

        $data = [
            "id"                => $this->id,
            "reason"            => $this->reason,
            "unban_reason"      => $this->unban_reason ?? "",
            "banned_id"         => $this->banned_id,
            "banned_name"       => $banned->name,
            "banned_img"        => MediaResource::make($banned->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "banned_by_id"      => $this->banned_by_id,
            "banned_by_name"    => $bannedBy->name,
            "banned_by_img"     => MediaResource::make($bannedBy->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "unbanned_by_id"    => $this->unbanned_by_id,
            "unbanned_by_name"  => $unbannedBy?->name ?? "",
            "unbanned_by_img"   => MediaResource::make($unbannedBy?->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "created_at"        => Carbon::parse($this->created_at)->translatedFormat('Y-m-d g:i A'),
            "banned_until"      => Carbon::parse($this->banned_until)->format("Y-m-d H:i"),
        ];

        return $data;
    }

    protected function extendForHttp(array $data, $request)
    {
        // No route-specific logic for this resource
        return $data;
    }
}
