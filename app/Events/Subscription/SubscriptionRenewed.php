<?php

declare(strict_types=1);

namespace App\Events\Subscription;

use App\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Fired after a subscription renewal is created. */
final class SubscriptionRenewed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Subscription $newSubscription,
        public readonly Subscription $previousSubscription,
    ) {}
}
