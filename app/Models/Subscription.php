<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubscriptionStatus;
use App\Enums\TenantPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Subscription — one row per billing period per tenant.
 *
 * @property string                    $id          ULID
 * @property int                       $tenant_id
 * @property string                    $plan        TenantPlan enum value
 * @property SubscriptionStatus        $status
 * @property \Carbon\CarbonImmutable   $starts_at
 * @property \Carbon\CarbonImmutable   $ends_at
 * @property string                    $amount_paid DECIMAL(20,6)
 * @property int|null                  $currency_id
 * @property string|null               $discount_id ULID FK
 * @property string                    $discount_amount DECIMAL(20,6)
 * @property string                    $billing_cycle monthly|annual
 */
class Subscription extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tenant_id', 'plan', 'status', 'starts_at', 'ends_at', 'trial_ends_at',
        'amount_paid', 'currency_id', 'discount_id', 'discount_amount',
        'billing_cycle', 'payment_reference', 'payment_method', 'metadata',
        'cancelled_at', 'cancellation_reason', 'lapsed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'        => SubscriptionStatus::class,
            'plan'          => TenantPlan::class,
            'starts_at'     => 'immutable_date',
            'ends_at'       => 'immutable_date',
            'trial_ends_at' => 'immutable_date',
            'cancelled_at'  => 'immutable_datetime',
            'lapsed_at'     => 'immutable_datetime',
            'amount_paid'   => 'decimal:6',
            'discount_amount' => 'decimal:6',
            'metadata'      => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $sub): void {
            if (empty($sub->id)) {
                $sub->id = (string) Str::ulid();
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function discountUsages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::Active->value);
    }

    public function scopeLapsed($query)
    {
        return $query->where('status', SubscriptionStatus::Lapsed->value);
    }

    /**
     * Subscriptions that should be marked as lapsed:
     * ended more than X days ago and still active/past_due.
     */
    public function scopeExpiredAndNotLapsed($query, int $graceDays = 0)
    {
        return $query
            ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::PastDue->value])
            ->where('ends_at', '<', now()->subDays($graceDays));
    }

    /**
     * Subscriptions that lapsed 30–60 days ago (win-back window).
     * Used by the WinbackDiscountJob to identify candidates.
     */
    public function scopeWinbackCandidates($query, int $lapsedDaysMin = 30, int $lapsedDaysMax = 60)
    {
        return $query
            ->where('status', SubscriptionStatus::Lapsed->value)
            ->where('lapsed_at', '<=', now()->subDays($lapsedDaysMin))
            ->where('lapsed_at', '>=', now()->subDays($lapsedDaysMax));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status->isAccessAllowed() && $this->ends_at->isFuture();
    }

    public function daysRemaining(): int
    {
        return (int) max(0, now()->diffInDays($this->ends_at, false));
    }
}
