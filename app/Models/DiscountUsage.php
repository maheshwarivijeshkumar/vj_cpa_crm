<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DiscountUsage — immutable redemption record.
 * Once written, this row must never be modified or deleted.
 */
class DiscountUsage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'discount_id', 'tenant_id', 'subscription_id',
        'original_amount', 'discount_amount', 'final_amount', 'used_at',
    ];

    protected function casts(): array
    {
        return [
            'original_amount' => 'decimal:6',
            'discount_amount' => 'decimal:6',
            'final_amount'    => 'decimal:6',
            'used_at'         => 'immutable_datetime',
        ];
    }

    protected static function booted(): void
    {
        // Immutable — no updates or deletes allowed
        static::updating(static fn () => false);
        static::deleting(static fn () => false);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
