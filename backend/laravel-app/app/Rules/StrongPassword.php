<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    public function passes($attribute, $value)
    {
        // Enforce password rules:
        // - Minimum 8 characters
        // - At least one uppercase letter
        // - At least one special character
        return preg_match('/^(?=.*[A-Z])(?=.*[\W]).{8,}$/', $value);
    }

    public function message()
    {
        return 'The :attribute must be at least 8 characters long, contain at least one uppercase letter and one special character.';
    }
}
