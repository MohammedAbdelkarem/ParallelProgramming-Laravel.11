<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseApiRequest;

class OTPRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            "userVerify" => $this->userVerifyRules(),
        };
    }

    public function userVerifyRules()
    {
        return [
            "otp"                   => ['required', 'string'],
            "notification_token"    => ["required", "nullable", "string"],
            "device_id"             => ["required", "nullable", "string"],
            "device_name"           => ["required", "string"],
        ];
    }

    public function messages()
    {
        return [];
    }
}
