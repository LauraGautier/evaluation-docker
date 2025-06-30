<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            Password::min(12) // NIST 2024-2025 recommande minimum 12
                ->max(64)     // Support des phrases de passe longues
                ->letters()   // Au moins une lettre
                ->mixedCase() // Majuscules ET minuscules
                ->numbers()   // Au moins un chiffre
                ->symbols(),  // Au moins un caractère spécial
            'confirmed'
        ];
    }
}
