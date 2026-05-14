<?php

namespace App\Http\Resources\System;

use App\Http\Resources\Administration\Profile\AdminListResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"             => $this->id,
            "key"            => __("system_settings.{$this->key}"),
            "value"          => $this->value,
            "updater"        => new AdminListResource($this->updated_by),
            "last_update_at" => Carbon::parse($this->updated_at)->translatedFormat("Y-m-d g:i a"),
        ];
    }
}
