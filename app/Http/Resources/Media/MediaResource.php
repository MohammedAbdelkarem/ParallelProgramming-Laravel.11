<?php

namespace App\Http\Resources\Media;

use App\Http\Resources\BaseJsonResource;

class MediaResource extends BaseJsonResource
{
    protected function resourceArray($request)
    {
        $data = [
            'id'      => $this->id,
            'url'     => $this->getUrl(),
            'type'    => getMediaType($this->mime_type),
            'quality' => $this->getCustomProperty('quality') ?? null,
        ];

        return $data;
    }

    protected function extendForHttp(array $data, $request)
    {
        // No route-specific logic needed for media
        return $data;
    }
}
