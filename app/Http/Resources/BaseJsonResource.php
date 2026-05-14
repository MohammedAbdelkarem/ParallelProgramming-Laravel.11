<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseJsonResource extends JsonResource
{
    public function toArray($request)
    {
        // Let the child resource build its data
        // If socket mode → return only the base data
        return isForSocket()
            ? $this->resourceArray($request)
            : $this->extendForHttp($this->resourceArray($request), $request);

    }

    /**
     * Child resources MUST implement this.
     */
    protected function resourceArray($request)
    {
        return [];
    }

    /**
     * Child resources may override this to add route‑specific data.
     */
    protected function extendForHttp(array $data, $request)
    {
        return $data;
    }
}
