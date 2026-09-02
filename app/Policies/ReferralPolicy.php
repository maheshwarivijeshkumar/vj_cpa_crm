<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Referral;
use App\Models\User;

/**
 * ReferralPolicy — referral links and history belong to tenants.
 */
final class ReferralPolicy
{
    /** Any firm user can view their firm's referral link and history. */
    public function viewAny(User $user): bool
    {
        return $user->isFirmUser();
    }

    /** Firm users can only view referrals where they are the referrer. */
    public function view(User $user, Referral $referral): bool
    {
        return $user->tenant_id === $referral->referrer_tenant_id;
    }
}
