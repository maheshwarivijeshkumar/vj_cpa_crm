<?php

declare(strict_types=1);

namespace App\Listeners\Subscription;

use App\Events\Subscription\SubscriptionCreated;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

/**
 * Sends a subscription confirmation notification to the firm owner
 * via NotificationService using the 'subscription.created' template.
 */
final class SendSubscriptionConfirmationEmail implements ShouldQueue
{
    public string $queue = 'notifications';
    public int    $tries = 3;

    public function __construct(private readonly NotificationService $notifications) {}

    public function handle(SubscriptionCreated $event): void
    {
        $sub    = $event->subscription;
        $tenant = $sub->tenant;

        if ($tenant === null) return;

        // Find the firm owner user
        $owner = DB::table('users')
            ->where('tenant_id', $tenant->id)
            ->where('user_type', 'firm_owner')
            ->where('status', 'active')
            ->first();

        if ($owner === null) return;

        try {
            $this->notifications->send(
                \App\Models\User::find($owner->id),
                'subscription.created',
                [
                    'firm.name'      => $tenant->name,
                    'plan'           => $sub->plan->label(),
                    'starts_at'      => $sub->starts_at->format('M j, Y'),
                    'ends_at'        => $sub->ends_at->format('M j, Y'),
                    'amount'         => number_format((float) $sub->amount_paid, 2),
                    'billing_cycle'  => $sub->billing_cycle,
                ],
            );
        } catch (\Throwable $e) {
            logger()->error('SendSubscriptionConfirmationEmail failed', ['error' => $e->getMessage()]);
        }
    }
}
