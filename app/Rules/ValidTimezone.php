<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * ValidTimezone — ensures the submitted value is a valid IANA timezone identifier.
 *
 * Accepts both:
 *  - IANA strings: 'America/Toronto', 'Europe/London'
 *  - timezone_id FK integers: 42 (looks up zone_name in the timezones table)
 */
final class ValidTimezone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_int($value) || ctype_digit((string) $value)) {
            // FK integer — validate that the row exists
            $exists = \Illuminate\Support\Facades\DB::table('timezones')
                ->where('id', (int) $value)
                ->where('is_active', true)
                ->exists();

            if (! $exists) {
                $fail('The selected :attribute is not a valid timezone.');
            }

            return;
        }

        // IANA string validation
        if (! is_string($value)) {
            $fail('The :attribute must be a valid timezone string or timezone ID.');
            return;
        }

        try {
            new \DateTimeZone($value);
        } catch (\Exception) {
            $fail("The :attribute '{$value}' is not a recognized timezone identifier.");
        }
    }
}
