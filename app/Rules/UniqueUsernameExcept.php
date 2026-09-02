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
final class UniqueUsernameExcept implements ValidationRule
{
    public function __construct(private readonly ?int $excludeId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid username.');
            return;
        }

        $query = DB::table('users')
            ->whereNull('deleted_at')
            ->where('username', strtolower(trim($value)));

        if ($this->excludeId !== null) {
            $query->where('id', '!=', $this->excludeId);
        }

        if ($query->exists()) {
            $fail('The :attribute is already in use.');
        }
    }
}
