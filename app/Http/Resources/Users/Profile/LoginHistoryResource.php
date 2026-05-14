<?php

namespace App\Http\Resources\Users\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class LoginHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data =  [
            "id"            => $this->id,
            "country_code"  => $this->country_code ?? "N/A",
            "device"        => $this->device_name,
            "location"      => "{$this->country} / {$this->city}",
            "created_at"    => Carbon::parse($this->created_at)->translatedFormat('Y-m-d g:i A'),
        ];
        if (auth()->user()->role_id != 3)
            $data['ip'] = $this->ip_address;
        return $data;
    }
}