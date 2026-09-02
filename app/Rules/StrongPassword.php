<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * StrongPassword rule — enforces password complexity beyond Laravel's default.
 *
 * Requirements:
 *  - Minimum 8 characters
 *  - At least one uppercase letter
 *  - At least one lowercase letter
 *  - At least one number
 *  - At least one special character
 *  - Not in common password list
 */
final class StrongPassword implements ValidationRule
{
    private const COMMON_PASSWORDS = [
        'password', 'password1', '123456789', 'qwerty123',
        'letmein1', 'admin123', 'welcome1', 'password123',
    ];

    public function __construct(private readonly int $minLength = 8) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        if (strlen($value) < $this->minLength) {
            $fail("The :attribute must be at least {$this->minLength} characters.");
            return;
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('The :attribute must contain at least one uppercase letter.');
            return;
        }

        if (! preg_match('/[a-z]/', $value)) {
            $fail('The :attribute must contain at least one lowercase letter.');
            return;
        }

        if (! preg_match('/[0-9]/', $value)) {
            $fail('The :attribute must contain at least one number.');
            return;
        }

        if (! preg_match('/[^a-zA-Z0-9]/', $value)) {
            $fail('The :attribute must contain at least one special character.');
            return;
        }

        if (in_array(strtolower($value), self::COMMON_PASSWORDS, true)) {
            $fail('The :attribute is too common. Please choose a more unique password.');
        }
    }
}
