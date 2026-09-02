<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Referral;
use App\Models\Tenant;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * ReferralRewardMail
 *
 * Sent to a referrer (firm owner) when their referred contact
 * has verified their account and the reward has been credited.
 * Informs them of the reward type, amount, and how to redeem it.
 */
final class ReferralRewardMail extends BaseMailable
{
    public function __construct(
        public readonly Tenant   $referrer,
        public readonly Referral $referral,
        public readonly string   $rewardType,   // 'points' | 'credit'
        public readonly string   $rewardAmount,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You earned a referral reward — {$this->rewardAmount} {$this->rewardType}!",
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlTemplate: 'emails.referral.reward',
            textTemplate: 'emails.referral.reward-text',
            with: [
                'referrer'     => $this->referrer,
                'referral'     => $this->referral,
                'rewardType'   => ucfirst($this->rewardType),
                'rewardAmount' => $this->rewardAmount,
                'portalUrl'    => rtrim(config('app.url'), '/') . '/portal/referrals',
                'appName'      => config('app.name'),
            ],
        );
    }
}
