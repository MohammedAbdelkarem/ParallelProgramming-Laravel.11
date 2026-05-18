<?php
namespace App\Http\Requests\Users\Auth;

use App\Http\Requests\BaseApiRequest;
use App\Rules\PhoneNumberRule;
use Illuminate\Validation\Rule;

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
            "loginUser"    => $this->loginUserRules(),
            "registerUser" => $this->registerUserRules(),
        };
    }

    public function registerUserRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule(), Rule::unique('users', 'phone_number')],
            'email'        => ['required', 'email', Rule::unique('users', 'email')],
        ];
    }

    public function loginUserRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule(), Rule::exists('users', 'phone_number')->where('role_id', 3)],
        ];
    }

    public function messages()
    {
        return [];
    }
}
