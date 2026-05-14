<?php

namespace App\Http\Resources\List;

use App\Http\Resources\BaseJsonResource;

class ListResource extends BaseJsonResource
{
    protected function resourceArray($request)
    {
        $data = [
            'id'   => $this->id,
            'name' => $this->name,
        ];

        return $data;
    }

    protected function extendForHttp(array $data, $request)
    {
        // No route-specific extensions for this resource
        return $data;
    }
}
