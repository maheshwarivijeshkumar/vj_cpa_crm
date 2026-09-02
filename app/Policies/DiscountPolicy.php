<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Discount;
use App\Models\User;

/**
 * DiscountPolicy — discounts are platform-managed.
 * Platform admins bypass via Gate::before().
 * Firm users can only read discounts applicable to them (via DiscountService::validate).
 */
final class DiscountPolicy
{
    /** Only platform admins can list all discounts. */
    public function viewAny(User $user): bool
    {
        return false; // Gate::before handles platform admins
    }

    /** Firm users can view a discount only if it is applicable to their tenant. */
    public function view(User $user, Discount $discount): bool
    {
        return false; // Gate::before handles platform admins; firm users use validate() endpoint
    }

    public function create(User $user): bool  { return false; }
    public function update(User $user, Discount $discount): bool  { return false; }
    public function delete(User $user, Discount $discount): bool  { return false; }
    public function deactivate(User $user, Discount $discount): bool { return false; }
}
