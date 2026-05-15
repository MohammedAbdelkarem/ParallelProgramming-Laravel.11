<?php

namespace App\Http\Requests\Users\Auth;

use App\Enums\GenderEnum;
use App\Rules\PhoneNumberRule;
use App\Rules\ShiftsOverlappingRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;

class AuthRequest extends BaseApiRequest
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
            "loginUser" => $this->loginUserRules(),
        };
    }

    
    public function loginUserRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::exists('users' , 'phone_number')->where('role_id' , 3)],
        ];
    }

    public function messages()
    {
        return [];
    }
}
