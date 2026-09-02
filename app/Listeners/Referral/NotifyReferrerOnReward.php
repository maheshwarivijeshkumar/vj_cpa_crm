<?php

declare(strict_types=1);

namespace App\Listeners\Referral;

use App\Events\Referral\ReferralRewarded;
use App\Services\Notification\NotificationService;
use App\Services\Referral\ReferralService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * NotifyReferrerOnReward — sends the referrer a reward notification
 * showing their new points/credit balance.
 */
final class NotifyReferrerOnReward implements ShouldQueue
{
    public string $queue = 'notifications';
    public int    $tries = 3;

    public function __construct(
        private readonly NotificationService $notifications,
        private readonly ReferralService     $referralService,
    ) {}

    public function handle(ReferralRewarded $event): void
    {
        $referral = $event->referral;
        $tenant   = $referral->referrerTenant;

        if ($tenant === null) return;

        $owner = \App\Models\User::query()
            ->where('tenant_id', $tenant->id)
            ->where('user_type', 'firm_owner')
            ->where('status', 'active')
            ->first();

        if ($owner === null) return;

        $balance = $this->referralService->getBalance($tenant->id);

        try {
            $this->notifications->send(
                $owner,
                'referral.rewarded',
                [
                    'referee_name'   => $referral->refereeTenant?->name ?? 'a referred firm',
                    'points_earned'  => '500',
                    'credit_earned'  => '10.00',
                    'points_balance' => number_format((float) $balance['points']),
                    'credit_balance' => number_format((float) $balance['credit'], 2),
                ],
            );
        } catch (\Throwable $e) {
            logger()->error('NotifyReferrerOnReward failed', ['error' => $e->getMessage()]);
        }
    }
}
