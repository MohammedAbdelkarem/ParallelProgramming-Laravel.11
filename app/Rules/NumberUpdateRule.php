<?php

namespace App\Rules;

use App\Models\User;
use App\Models\Users\Profile\ArchivedUser;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NumberUpdateRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            User::withTrashed()->where('phone_number', $value)->exists()
            || ArchivedUser::where('phone_number', $value)->exists()
        ) {
            $fail(__("validation.Invalid phone number"));
        }
    }
}
