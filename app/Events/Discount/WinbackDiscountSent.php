<?php

declare(strict_types=1);

namespace App\Events\Discount;

use App\Models\Discount;
use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Fired after a win-back discount is generated and emailed to a lapsed tenant. */
final class WinbackDiscountSent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Discount $discount,
        public readonly Tenant   $tenant,
    ) {}
}
