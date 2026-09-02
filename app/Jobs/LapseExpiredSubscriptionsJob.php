<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SubscriptionStatus;
use App\Events\Subscription\SubscriptionLapsed;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * LapseExpiredSubscriptionsJob
 *
 * Runs daily. Finds all subscriptions whose end_date has passed and
 * whose status is still 'active' or 'cancelled' (past end date).
 * Transitions them to 'lapsed' and fires SubscriptionLapsed events so
 * the winback discount flow can be triggered.
 *
 * Queued: yes — never process large record sets synchronously.
 * Unique:  yes — prevent overlapping runs on a busy server.
 */
final class LapseExpiredSubscriptionsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maximum seconds before the job is considered stuck. */
    public int $timeout = 120;

    /** Retry once after a transient failure. */
    public int $tries = 2;

    public function __construct()
    {
        $this->onQueue('subscriptions');
    }

    public function handle(): void
    {
        $now = now();

        // Fetch in chunks to avoid loading all rows into memory at once.
        Subscription::query()
            ->whereIn('status', [
                SubscriptionStatus::Active->value,
                SubscriptionStatus::Cancelled->value,
            ])
            ->where('ends_at', '<', $now)
            ->whereNull('lapsed_at')
            ->chunkById(100, function (\Illuminate\Support\Collection $subscriptions) use ($now): void {
                foreach ($subscriptions as $subscription) {
                    $subscription->update([
                        'status'    => SubscriptionStatus::Lapsed,
                        'lapsed_at' => $now,
                    ]);

                    // Fire event — TriggerWinbackOnLapse listener will handle discount creation.
                    event(new SubscriptionLapsed($subscription));

                    Log::info('LapseExpiredSubscriptionsJob: subscription lapsed', [
                        'subscription_id' => $subscription->id,
                        'tenant_id'       => $subscription->tenant_id,
                        'ended_at'        => $subscription->ends_at->toDateString(),
                    ]);
                }
            });
    }

    /** Unique key: one run per server at a time. */
    public function uniqueId(): string
    {
        return 'lapse-expired-subscriptions';
    }
}
