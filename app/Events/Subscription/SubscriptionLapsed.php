<?php

declare(strict_types=1);

namespace App\Events\Subscription;

use App\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a subscription is marked as lapsed.
 * Listener: TriggerWinbackOnLapse — generates a win-back discount + emails tenant.
 */
final class SubscriptionLapsed
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Subscription $subscription) {}
}
