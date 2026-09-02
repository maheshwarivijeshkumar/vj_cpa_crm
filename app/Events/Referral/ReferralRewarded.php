<?php

declare(strict_types=1);

namespace App\Events\Referral;

use App\Models\Referral;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired after the referrer receives their reward.
 * Listener: NotifyReferrerOnReward — sends a congratulations email.
 */
final class ReferralRewarded
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Referral $referral) {}
}
