<?php

namespace App\Http\Requests\Administration\Auth;

use App\Http\Requests\BaseApiRequest;
use App\Rules\PhoneNumberRule;

class AuthRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            "login" => $this->loginRules(),
        };
    }

    public function loginRules()
    {
        return [
            "email"         => ['required', 'email', 'exists:users,email'],
            "password"      => ['required', 'string'],
            "device_name"   => ["required", "string"],
            "notification_token" => ["present", "nullable", "string"],
            // "device_id"     => ["required", "nullable", "string"],
        ];
    }

    public function messages()
    {
        return [
            "email.exists"  => __("exception_messages.invalid_credentials"),
        ];
    }
}