<?php

declare(strict_types=1);

namespace App\Filters;

use App\Enums\UserStatus;
use App\Enums\UserType;

/**
 * UserFilters — query filters for the users listing.
 * Each public method maps to a request parameter.
 */
final class UserFilters extends QueryFilter
{
    /** ?search= — full-text search across name, email, username */
    public function search(string $value): void
    {
        $this->query->where(function ($q) use ($value): void {
            $q->where('first_name', 'like', "%{$value}%")
              ->orWhere('last_name',  'like', "%{$value}%")
              ->orWhere('email',      'like', "%{$value}%")
              ->orWhere('username',   'like', "%{$value}%");
        });
    }

    /** ?user_type= — filter by UserType enum value */
    public function userType(string $value): void
    {
        if (UserType::tryFrom($value) !== null) {
            $this->query->where('user_type', $value);
        }
    }

    /** ?status= — filter by UserStatus enum value */
    public function status(string $value): void
    {
        if (UserStatus::tryFrom($value) !== null) {
            $this->query->where('status', $value);
        }
    }

    /** ?tenant_id= — scope to a specific tenant (platform admin view) */
    public function tenantId(string $value): void
    {
        if (is_numeric($value)) {
            $this->query->where('tenant_id', (int) $value);
        }
    }

    /** ?office_id= — scope to a specific office */
    public function officeId(string $value): void
    {
        if (is_numeric($value)) {
            $this->query->where('office_id', (int) $value);
        }
    }

    /** ?with_trashed=1 — include soft-deleted users */
    public function withTrashed(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->query->withTrashed();
        }
    }

    /** ?has_2fa=1 — only users with 2FA enabled */
    public function has2fa(string $value): void
    {
        $enabled = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $this->query->where('two_factor_enabled', $enabled);
    }
}
