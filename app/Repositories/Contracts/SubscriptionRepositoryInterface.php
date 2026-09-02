<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Subscription;
use Illuminate\Support\Collection;

interface SubscriptionRepositoryInterface extends RepositoryInterface
{
    /** Current active or trial subscription for a tenant (or null). */
    public function currentForTenant(int $tenantId): ?Subscription;

    /** All subscriptions for a tenant ordered by starts_at desc. */
    public function historyForTenant(int $tenantId): Collection;

    /** Subscriptions that have ended but not yet been marked lapsed. */
    public function findExpiredUnlapsed(int $graceDays = 0): Collection;

    /** Subscriptions that lapsed 30-60 days ago (win-back candidates). */
    public function findWinbackCandidates(int $minDays = 30, int $maxDays = 60): Collection;

    /** Count tenants currently on a given plan. */
    public function countByPlan(string $plan): int;

    /** Monthly Recurring Revenue snapshot (sum of active subscription amounts). */
    public function mrrSnapshot(): string;
}
