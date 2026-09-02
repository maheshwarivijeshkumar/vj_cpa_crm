<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Discount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DiscountRepositoryInterface extends RepositoryInterface
{
    /** Find a discount by its code (case-insensitive). */
    public function findByCode(string $code): ?Discount;

    /** All active, usable discounts applicable to a given tenant + plan. */
    public function usableForTenant(int $tenantId, string $plan): Collection;

    /** Increment the global uses_count counter atomically. */
    public function incrementUsesCount(string $discountId): void;

    /** Sync the specific-tenant assignment pivot for a discount. */
    public function syncTenantAssignments(string $discountId, array $tenantIds): void;

    /** Discounts about to expire within $days days (for warning jobs). */
    public function expiringWithin(int $days): Collection;

    /** Total amount saved via a specific discount across all redemptions. */
    public function totalSaved(string $discountId): string;
}
