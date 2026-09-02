<?php

declare(strict_types=1);

namespace App\Filters;

use App\Enums\SubscriptionStatus;
use App\Enums\TenantPlan;

/** Query filters for the subscription listing. */
final class SubscriptionFilters extends QueryFilter
{
    /** ?status= */
    public function status(string $value): void
    {
        if (SubscriptionStatus::tryFrom($value) !== null) {
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

    /** ?tenant_id= */
    public function tenantId(string $value): void
    {
        if (is_numeric($value)) {
            $this->query->where('tenant_id', (int) $value);
        }
    }

    /** ?billing_cycle=monthly|annual */
    public function billingCycle(string $value): void
    {
        if (in_array($value, ['monthly', 'annual'], true)) {
            $this->query->where('billing_cycle', $value);
        }
    }

    /** ?expiring_days=7 — subscriptions ending within N days */
    public function expiringDays(string $value): void
    {
        if (is_numeric($value)) {
            $this->query
                ->where('status', 'active')
                ->where('ends_at', '<=', now()->addDays((int) $value));
        }
    }
}
