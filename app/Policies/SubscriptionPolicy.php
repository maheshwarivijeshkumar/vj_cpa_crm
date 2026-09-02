<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

/**
 * SubscriptionPolicy — tenants manage their own subscriptions.
 * Platform admins bypass via Gate::before().
 */
final class SubscriptionPolicy
{
    /** Firm owners can view their own subscription history. */
    public function viewAny(User $user): bool
    {
        return $user->isFirmUser();
    }

    /** Firm users can view their own tenant's subscriptions. */
    public function view(User $user, Subscription $subscription): bool
    {
        return $user->tenant_id === $subscription->tenant_id;
    }

    /** Only platform admins can create subscriptions for tenants directly. */
    public function create(User $user): bool
    {
        return false; // Gate::before handles platform admins
    }

    /** Firm owners can cancel their own subscription. */
    public function cancel(User $user, Subscription $subscription): bool
    {
        return $user->tenant_id === $subscription->tenant_id
            && $user->user_type === 'firm_owner';
    }
}
