<?php

declare(strict_types=1);

namespace App\Events\Discount;

use App\Models\Discount;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Fired after a discount is redeemed against a subscription. */
final class DiscountApplied
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Discount $discount,
        public readonly int      $tenantId,
        public readonly string   $discountAmount, // DECIMAL string
    ) {}
}
