<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * ReferralLink — unique shareable link per tenant.
 * A tenant has exactly one active referral link at any time.
 */
class ReferralLink extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tenant_id', 'code', 'click_count', 'signup_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'click_count'  => 'integer',
            'signup_count' => 'integer',
            'is_active'    => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $link): void {
            if (empty($link->id)) {
                $link->id = (string) Str::ulid();
            }
            if (empty($link->code)) {
                $link->code = 'REF-' . strtoupper(Str::random(6));
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function pendingReferrals(): HasMany
    {
        return $this->hasMany(Referral::class)->where('status', 'pending');
    }

    /** Full URL for the referral link */
    public function url(): string
    {
        return rtrim(config('app.url', url('/')), '/') . '/r/' . $this->code;
    }

    /** Increment click count without touching updated_at */
    public function recordClick(): void
    {
        static::withoutTimestamps(fn () => $this->increment('click_count'));
    }
}
