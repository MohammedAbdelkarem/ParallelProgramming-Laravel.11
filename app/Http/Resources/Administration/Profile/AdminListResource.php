<?php

namespace App\Http\Resources\Administration\Profile;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\Media\MediaResource;

class AdminListResource extends BaseJsonResource
{
    protected function resourceArray($request)
    {
        $data = [
            "id"            => $this->id,
            "name"          => $this->name,
            "role_name"     => $this->role->name,
            "role_id"     => $this->role->id,
            "avatar"        => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "email"         => $this->email,
            "is_active"     => (bool) !$this->deactive_at,
            "created_at"    => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i a"),
        ];

        return $data;
    }

    protected function extendForHttp(array $data, $request)
    {
        // No route-specific logic for this resource
        return $data;
    }
}
