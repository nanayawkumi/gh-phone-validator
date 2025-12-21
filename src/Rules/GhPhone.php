<?php

namespace Nanayawkumi\GhPhoneValidator\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

class GhPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!GhPhoneValidator::normalize((string) $value)) {
            $fail('The :attribute must be a valid Ghana phone number.');
        }
    }
}