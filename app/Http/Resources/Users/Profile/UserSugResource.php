<?php

namespace App\Http\Resources\Users\Profile;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\Wallet\WalletResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSugResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        // if (!$this->phone_number && auth()->user() && auth()->user()->isAdmin())
        //     $phone_number = $this->archivedAccount->phone_number;
        // else {
        //     $phone_number = $this->phone_number ?? "";
        // }

            $phone_number = $this->phone_number ?? "";
        $data =  [
            "id"            => $this->id,
            "name"          => $this->name,
            "email"         => $this->email,
            "avatar"        => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "phone_number"  => $phone_number,
            "role_id"       => $this->role_id,
            "role_name"     => $this->role->name,
            "account_status" => $this->account_status,
        ];
        return $data;
    }
}