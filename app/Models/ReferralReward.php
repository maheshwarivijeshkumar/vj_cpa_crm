<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RewardType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ReferralReward — append-only reward ledger.
 * Balance is always derived by summing earn/spend entries.
 * This row is IMMUTABLE once written — no updates or deletes.
 */
class ReferralReward extends Model
{
    /** No updated_at — append-only */
    const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id', 'referral_id', 'reward_type', 'amount',
        'currency_id', 'description', 'entry_type', 'balance_after',
    ];

    protected function casts(): array
    {
        return [
            'reward_type'   => RewardType::class,
            'amount'        => 'decimal:6',
            'balance_after' => 'decimal:6',
            'created_at'    => 'immutable_datetime',
        ];
    }

    protected static function booted(): void
    {
        // Immutable — append-only ledger
        static::updating(static fn () => false);
        static::deleting(static fn () => false);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
