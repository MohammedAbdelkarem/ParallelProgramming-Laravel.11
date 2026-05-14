<?php

namespace App\Http\Resources\Users\Profile;

use Carbon\Carbon;
use App\Traits\ImagesHelper;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\DoctorResouce;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        if (!$this->phone_number && auth()->user() && auth()->user()->isAdmin())
            $phone_number = $this->archivedAccount->phone_number;
        else {
            $phone_number = $this->phone_number ?? "";
        }

        $data = [
            "id"                => $this->id,
            "name"              => $this->name,
            "avatar"            => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "is_male"           => $this->is_male ? (bool) $this->is_male : null,
            "phone_number"      => $phone_number,
            "role"              => $this->role->name,
            "created_at"        => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i a"),
        ];

        //Admin Info
        if (auth()->user()->isRegularAdmin()) {
            $data += [
                "in_trash"      => (bool) $this->deleted_at,
                "is_active"     => (bool) !$this->deactive_at,
                "is_banned"     => (bool) ($this->profile->banned_until && Carbon::parse($this->profile->banned_until)->gt(Carbon::now())),
            ];
        }

        return $data;
    }
}