<?php

namespace App\Http\Resources\Media;

use App\Constants\RouteNames;
use App\Enums\MediaTypeEnum;
use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefaultMediaResource extends BaseJsonResource
{
    protected function resourceArray($request)
    {
        $data = [
            'id'      => 0,
            'url'     => config('app.url') . '/' . config('_custom.lesson_default_video'),
            'type'    => MediaTypeEnum::VIDEO,
            'quality' => '720',
        ];

        return $data;
    }

    protected function extendForHttp(array $data, $request)
    {
        // No route‑specific logic for this resource
        return $data;
    }
}
