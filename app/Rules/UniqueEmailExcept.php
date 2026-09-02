<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

/**
 * UniqueEmailExcept — validates email uniqueness in the users table
 * while excluding a specific user ID (for update operations).
 *
 * Usage:
 *   new UniqueEmailExcept($user->id)
 *   new UniqueEmailExcept()  ← for create (no exclusion)
 */
final class UniqueEmailExcept implements ValidationRule
{
    public function __construct(private readonly ?int $excludeId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid email address.');
            return;
        }

        $query = DB::table('users')
            ->whereNull('deleted_at')
            ->where('email', strtolower(trim($value)));

        if ($this->excludeId !== null) {
            $query->where('id', '!=', $this->excludeId);
        }

        if ($query->exists()) {
            $fail('The :attribute address is already in use.');
        }
    }
}
