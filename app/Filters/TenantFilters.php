<?php

declare(strict_types=1);

namespace App\Filters;

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;

/** Query filters for the tenants listing (platform admin). */
final class TenantFilters extends QueryFilter
{
    /** ?search= — search across name, email, slug */
    public function search(string $value): void
    {
        $this->query->where(function ($q) use ($value): void {
            $q->where('name',  'like', "%{$value}%")
              ->orWhere('email', 'like', "%{$value}%")
              ->orWhere('slug',  'like', "%{$value}%");
        });
    }

    /** ?status= */
    public function status(string $value): void
    {
        if (TenantStatus::tryFrom($value) !== null) {
            $this->query->where('status', $value);
        }
    }

    /** ?plan= */
    public function plan(string $value): void
    {
        if (TenantPlan::tryFrom($value) !== null) {
            $this->query->where('plan', $value);
        }
    }

    /** ?country_id= */
    public function countryId(string $value): void
    {
        if (is_numeric($value)) {
            $this->query->where('country_id', (int) $value);
        }
    }

    /** ?trial_expiring=1 — trial tenants expiring in next 7 days */
    public function trialExpiring(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->query
                ->where('status', 'trial')
                ->where('trial_ends_at', '<=', now()->addDays(7))
                ->where('trial_ends_at', '>', now());
        }
    }

    /** ?with_trashed=1 */
    public function withTrashed(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->query->withTrashed();
        }
    }
}
