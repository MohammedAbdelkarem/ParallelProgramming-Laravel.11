<?php

namespace App\Http\Requests\Users\Profile;

use App\Http\Requests\BaseApiRequest;
use App\Rules\PhoneNumberRule;
use Illuminate\Validation\Rule;

class ProfileRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'completeProfile'       => $this->completeProfileRules(),
            'update'                => $this->updateProfileRules(),
            'updateProfileImage'    => $this->updateProfileImageRules(),
            'changeLang'            => $this->changeLangRule(),
            'deleteProfile'         => $this->deleteProfileRules(),
        };
    }

    public function completeProfileRules()
    {
        return [
            "name"       => ['required', 'string', 'between:1,100'],
            "birth_date" => ["present", "nullable", "date", "after_or_equal:1930-01-01"],
            "is_male"    => ["present", "nullable", "boolean"],
            "email"      => ["present", "nullable", "email", Rule::unique("users", "email")->where('role_id' , auth()->user()->role_id)],
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

    public function updateProfileRules(): array
    {
        return [
            'name'          => ['required', 'string', 'between:1,100'],
            "birth_date"    => ["present", "nullable", "date", "after_or_equal:1930-01-01"],
            "is_male"       => ["present", "nullable", "boolean"],
            "email"         => ["present", "nullable", "email", Rule::unique("users", "email")->where('role_id' , auth()->user()->role_id)->ignore(auth()->id())],
            "delete_image" => ["present", "nullable", "boolean"],
            "avatar" => [
                "nullable",
                "file",
                "image",
                "max:1024",
                "dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048",
                "mimes:png,jpg,jpeg,webpm"
            ],
            "logo" => [
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

    public function deleteProfileRules(): array
    {
        return [
            "phone_number" => ['required', 'exists:users,phone_number,id,' . auth()->id(), new PhoneNumberRule()],
        ];
    }

    public function messages()
    {
        return [];
    }
}