<?php

namespace App\Http\Requests\System\Notification;

use App\Enums\Notifications\NotificationTypes;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class NotificationRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            "storePrivate" => $this->storePrivateRules(),
            "storePublic" => $this->storePublicRules(),
        };
    }

    public function storePrivateRules()
    {
        return [
            'title' => ['required', 'string', 'max:80',],
            'body' => ['required', 'max:650', 'string'],
            'type' => ['required', 'string', Rule::in(NotificationTypes::values())],
            'ids_list' => ['present', 'nullable', 'array'],
            'ids_list.*' => ['required', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'target_users' => ['present', 'required_without:ids_list', 'array', 'nullable'],
            'target_users.*' => ['required_without:ids_list', 'integer', 'exists:roles,id'],
        ];
    }

    public function storePublicRules()
    {
        return [
            'title' => ['required', 'string', 'max:80',],
            'body' => ['required', 'max:650', 'string'],
        ];
    }

    public function messages()
    {
        return [];
    }
}
