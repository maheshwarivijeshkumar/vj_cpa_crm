<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReferralStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Referral — one row per referred signup attempt.
 * Created when a prospect clicks a referral link; status progresses
 * through pending → signed → verified → rewarded (or expired/revoked).
 */
class Referral extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'referrer_tenant_id', 'referral_link_id', 'referee_tenant_id',
        'referee_email', 'referee_ip', 'status',
        'clicked_at', 'signed_up_at', 'verified_at', 'rewarded_at',
        'expires_at', 'revoke_reason', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status'      => ReferralStatus::class,
            'clicked_at'  => 'immutable_datetime',
            'signed_up_at'=> 'immutable_datetime',
            'verified_at' => 'immutable_datetime',
            'rewarded_at' => 'immutable_datetime',
            'expires_at'  => 'immutable_datetime',
            'metadata'    => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $referral): void {
            if (empty($referral->id)) {
                $referral->id = (string) Str::ulid();
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function referrerTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'referrer_tenant_id');
    }

    public function refereeTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'referee_tenant_id');
    }

    public function referralLink(): BelongsTo
    {
        return $this->belongsTo(ReferralLink::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', ReferralStatus::Pending->value);
    }

    public function scopeRewardable($query)
    {
        return $query->where('status', ReferralStatus::Verified->value);
    }

    public function scopeExpiring($query)
    {
        return $query
            ->where('status', ReferralStatus::Pending->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function canBeRewarded(): bool
    {
        return $this->status === ReferralStatus::Verified;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
