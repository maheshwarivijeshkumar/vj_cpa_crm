<?php

declare(strict_types=1);

namespace App\Events\Referral;

use App\Models\Referral;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Fired after a referred tenant completes registration. */
final class ReferralSignedUp
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Referral $referral) {}
}
