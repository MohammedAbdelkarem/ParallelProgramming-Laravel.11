<?php

namespace App\Http\Requests\Administration\Profile;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class UserProfileRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            "restore" => $this->restoreRules(),
        };
    }

    public function restoreRules()
    {
        return [
            "user_id"   => ["required", Rule::exists('users', 'id')->where('role_id', 3)->whereNotNull('deleted_at')],
        ];
    }

    public function messages()
    {
        return [];
    }
}
