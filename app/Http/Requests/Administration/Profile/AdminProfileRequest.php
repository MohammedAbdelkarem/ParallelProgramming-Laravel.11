<?php

namespace App\Http\Requests\Administration\Profile;

use App\Http\Requests\BaseApiRequest;
use App\Rules\PhoneNumberRule;
use Illuminate\Validation\Rule;

class AdminProfileRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'store'              =>  $this->storeProfileRules(),
            'update'             =>  $this->updateProfileRules(),
            'updateProfileImage' =>  $this->updateProfileImageRules(),
            'changeLang'         => $this->changeLangRule(),
        };
    }

    public function storeProfileRules(): array
    {
        return [
            "role_id"       => ["required", "exists:roles,id", "not_in:3"],
            "name"          => ["required", "string", "between:2,100"],
            "birth_date"    => ["present", "nullable", "date", "after_or_equal:1930-01-01"],
            "is_male"       => ["present", "nullable", "boolean"],
            "email"         => ["required", "email", "unique:users,email"],
            "password"      => ["required", "string", "min:8", "max:100"],
            "phone_number"  => ["present", "nullable", "string", "unique:users,phone_number", new PhoneNumberRule()],
            "avatar"        => [
                "nullable",
                "file",
                "image",
                "max:1024",
                "dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048",
                "mimes:png,jpg,jpeg,webpm"
            ],
        ];
    }

    public function updateProfileRules(): array
    {
        return [
            "role_id"       => ["required", "exists:roles,id", "not_in:3"],
            "name"          => ["required", "string", "between:2,100"],
            "birth_date"    => ["present", "nullable", "date", "after_or_equal:1930-01-01"],
            "is_male"       => ["present", "nullable", "boolean"],
            "email"         => ["required", "email", Rule::unique('users', 'email')->ignore($this->id)],
            "password"      => ["present", "nullable", "string", "min:8", "max:100"],
            "phone_number"  => ["present", "nullable", "string", new PhoneNumberRule(), Rule::unique("users", "phone_number")->ignore($this->id)],
            "delete_image"  => ["present", "nullable", "boolean"],
            "avatar"        => [
                "nullable",
                "file",
                "image",
                "max:1024",
                "dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048",
                "mimes:png,jpg,jpeg,webpm"
            ],
        ];
    }

    public function updateProfileImageRules(): array
    {
        return [
            "delete_image" => ["present", "nullable", "boolean"],
            "avatar" => [
                "nullable",
                "file",
                "image",
                "max:1024",
                "dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048",
                "mimes:png,jpg,jpeg,webpm"
            ],
        ];
    }

    public function changeLangRule(): array
    {
        return [
            "lang" => [
                "required",
                "string",
                Rule::in(config("_custom.accepted_languages"))
            ],
        ];
    }

    public function messages()
    {
        return [];
    }
}
