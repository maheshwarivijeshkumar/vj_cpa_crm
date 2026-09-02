<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ReferralStatus;
use App\Models\Referral;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * CleanupExpiredReferralsJob
 *
 * Runs daily. Finds referral records that:
 *  - Are still in 'clicked' or 'signed_up' status (i.e., not yet rewarded/expired)
 *  - Have passed their expires_at timestamp
 *
 * Transitions them to 'expired' status so reports stay accurate and
 * unverified referrals do not linger indefinitely.
 */
final class CleanupExpiredReferralsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;
    public int $tries   = 2;

    public function __construct()
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $now = now();
        $affected = 0;

        Referral::query()
            ->whereIn('status', [
                ReferralStatus::Pending->value,
                ReferralStatus::Signed->value,
            ])
            ->where('expires_at', '<', $now)
            ->chunkById(200, function (\Illuminate\Support\Collection $referrals) use (&$affected): void {
                foreach ($referrals as $referral) {
                    $referral->update(['status' => ReferralStatus::Expired]);
                    $affected++;
                }
            });

        Log::info('CleanupExpiredReferralsJob: completed', [
            'expired_count' => $affected,
            'ran_at'        => $now->toIso8601String(),
        ]);
    }

    public function uniqueId(): string
    {
        return 'cleanup-expired-referrals';
    }
}
