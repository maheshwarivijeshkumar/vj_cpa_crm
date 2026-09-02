<?php

declare(strict_types=1);

namespace App\Events\Discount;

use App\Models\Discount;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Fired after a discount is created. Listener: SendDiscountEmail (if auto_email = true). */
final class DiscountCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Discount $discount) {}
}
