<?php

namespace App\Http\Resources\Users\Profile;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (!$this->phone_number && auth()->user() )
            $phone_number = $this->archivedAccount->phone_number;
        else {
            $phone_number = $this->phone_number ?? "";
        }


        // dd(auth()->id() , $this->id);
        $data = [
            "id"        => $this->id,
            "name"      => $this->name,
            "avatar" => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "ban"           => (auth()->id() == $this->id ) ? $this->getBanData() : null,
            "birth_date"    => $this->birth_date ?? "",
            "is_male"       => !is_null($this->is_male) ? (bool) $this->is_male : null,
            "email"         => $this->email ?? "",
            "phone_number"  => $phone_number,
            "role_id"  => $this->role_id,
            "created_at"           => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i a"),
            "active_notifications" => (bool) $this->active_notifications,
        ];

        return $data;
    }

    public function getAdminData()
    {
        return [
            "phone_number"  => $this->phone_number ?? $this->archivedAccount->phone_number,
            "in_trash"      => (bool) $this->deleted_at,
            "deleted_at"    => $this->deleted_at ?? "",
            "is_active"     => (bool) !$this->deactive_at,
            "deactive_at"   => $this->deactive_at ?? "",
        ];
    }

    public function getBanData()
    {
        $isBanned = (bool)($this->profile->banned_until && Carbon::parse($this->profile->banned_until)->gt(Carbon::now()));

        return [
            "is_banned"     => $isBanned,
            "banned_until"  => $isBanned
                ? Carbon::parse($this->profile->banned_until)->translatedFormat("y-m-d g:i a")
                : "",
            "ban_reason"    => ($isBanned && $this->bans) ?  $this->bans[0]["reason"] : "",
        ];
    }
}
