<?php

namespace App\Http\Requests\Users\Profile;

use App\Http\Requests\BaseApiRequest;
use App\Rules\NumberUpdateRule;
use App\Rules\PhoneNumberRule;
use Illuminate\Validation\Rule;

class NumberUpdateRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            "updateNumber" => $this->updateNumberRules(),
            "verifyNumber" => $this->verifyNumberRules(),
        };
    }

    public function updateNumberRules()
    {
        return [
            "phone_number" => ["required", new PhoneNumberRule(), new NumberUpdateRule()]
        ];
    }

    public function verifyNumberRules()
    {
        return [
            "otp" => ["required", Rule::exists("number_updates", "otp")->where('user_id', auth()->id())],
        ];
    }

    public function messages()
    {
        return [];
    }
}
