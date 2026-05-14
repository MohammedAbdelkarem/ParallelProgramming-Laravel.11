<?php

namespace App\Http\Resources\Administration\Profile;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\Media\MediaResource;

class AdminProfileResource extends BaseJsonResource
{
    protected function resourceArray($request)
    {
        $data = [
            "is_me"                => $this->id == auth()->id(),
            "id"                   => $this->id,
            "name"                 => $this->name,
            "role_id"              => $this->role_id,
            "role_name"            => $this->role->name,
            "birth_date"           => $this->birth_date ?? "",
            "is_male"              => !is_null($this->is_male) ? (bool) $this->is_male : null,
            "email"                => $this->email ?? "",
            "phone_number"         => $this->phone_number,
            "avatar"               => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "active_notifications" => (bool) $this->active_notifications,
            "deactive_at"          => $this->deactive_at
                                        ? Carbon::parse($this->deactive_at)->translatedFormat("Y-m-d g:i a")
                                        : "",
            "is_active"            => (bool) !$this->deactive_at,
            "created_at"           => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i a"),
            "updated_at"           => Carbon::parse($this->updated_at)->translatedFormat("Y-m-d g:i a"),
        ];

        return $data;
    }

    protected function extendForHttp(array $data, $request)
    {
        // No route-specific logic for this resource
        return $data;
    }
}
