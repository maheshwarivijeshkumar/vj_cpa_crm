<?php

declare(strict_types=1);

namespace App\Listeners\Subscription;

use App\Events\Subscription\SubscriptionLapsed;
use App\Models\Tenant;
use App\Services\Discount\DiscountService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * TriggerWinbackOnLapse — generates a win-back discount for the lapsed tenant.
 * Runs async. DiscountService::generateWinback() handles the email via
 * WinbackDiscountSent event → SendDiscountEmail listener.
 */
final class TriggerWinbackOnLapse implements ShouldQueue
{
    public string $queue = 'notifications';
    public int    $tries = 3;

    public function __construct(private readonly DiscountService $discountService) {}

    public function handle(SubscriptionLapsed $event): void
    {
        $tenant = Tenant::find($event->subscription->tenant_id);

        if ($tenant === null) {
            return;
        }

        try {
            $this->discountService->generateWinback($tenant);
        } catch (\RuntimeException) {
            // Tenant already has an active win-back code — skip silently
        }
    }
}
