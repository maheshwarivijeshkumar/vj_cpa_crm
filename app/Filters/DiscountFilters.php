<?php

declare(strict_types=1);

namespace App\Filters;

use App\Enums\DiscountStatus;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;

/** Query filters for the discount listing (platform admin). */
final class DiscountFilters extends QueryFilter
{
    /** ?search= — code, name */
    public function search(string $value): void
    {
        $this->query->where(fn ($q) => $q
            ->where('code', 'like', "%{$value}%")
            ->orWhere('name', 'like', "%{$value}%"),
        );
    }

    /** ?status= */
    public function status(string $value): void
    {
        if (DiscountStatus::tryFrom($value) !== null) {
            $this->query->where('status', $value);
        }
    }

    /** ?type= */
    public function type(string $value): void
    {
        if (DiscountType::tryFrom($value) !== null) {
            $this->query->where('type', $value);
        }
    }

    /** ?trigger= */
    public function trigger(string $value): void
    {
        if (DiscountTrigger::tryFrom($value) !== null) {
            $this->query->where('trigger', $value);
        }
    }

    /** ?active_only=1 — only usable (active + within valid window) */
    public function activeOnly(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->query->usable();
        }
    }

    /** ?expiring_days=7 — expiring within N days */
    public function expiringDays(string $value): void
    {
        if (is_numeric($value)) {
            $this->query
                ->whereNotNull('valid_until')
                ->where('valid_until', '<=', now()->addDays((int) $value));
        }
    }
}
