<?php

namespace App\Http\Requests\Administration\Log;

use App\Http\Requests\BaseApiRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class BanLogRequest extends BaseApiRequest
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
            "ban"   => $this->banRules(),
            "unBan" => $this->unbanRules(),
        };
    }

    public function banRules()
    {
        return [
            "user_id" => ["required", Rule::exists('users', 'id')->whereIn('role_id', [3 , 4])->whereNull('deleted_at')],
            "banned_until" => [
                "required",
                "date",
                "after:" . Carbon::now()->format('Y-m-d'),
                "before:" . Carbon::now()->addYears(500)->format("Y-m-d")
            ],
            "reason" => ["required", "string", "between:1,2000"],
        ];
    }

    public function unbanRules()
    {
        return [
            "user_id"       => ["required", Rule::exists('users', 'id')->whereIn('role_id', [3 , 4])->whereNull('deleted_at')],
            "unban_reason"  => ["present", "nullable", "string", "between:1,2000"],
        ];
    }

    public function messages()
    {
        return [];
    }
}