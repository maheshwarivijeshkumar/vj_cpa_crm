<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DiscountApplicability;
use App\Enums\DiscountStatus;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Discount model — platform-managed discount codes.
 *
 * @property string                    $id           ULID
 * @property int|null                  $created_by
 * @property string                    $code
 * @property string                    $name
 * @property DiscountType              $type
 * @property string                    $value        DECIMAL(20,6)
 * @property string|null               $max_discount_amount
 * @property int|null                  $currency_id
 * @property DiscountApplicability     $applicability
 * @property string|null               $applicable_plans
 * @property \Carbon\CarbonImmutable|null $valid_from
 * @property \Carbon\CarbonImmutable|null $valid_until
 * @property int|null                  $max_uses
 * @property int                       $max_uses_per_tenant
 * @property int                       $uses_count
 * @property DiscountTrigger           $trigger
 * @property DiscountStatus            $status
 * @property bool                      $auto_email
 */
class Discount extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'created_by', 'code', 'name', 'description', 'type', 'value',
        'max_discount_amount', 'currency_id', 'applicability', 'applicable_plans',
        'valid_from', 'valid_until', 'max_uses', 'max_uses_per_tenant',
        'uses_count', 'trigger', 'status', 'auto_email',
    ];

    protected function casts(): array
    {
        return [
            'type'                => DiscountType::class,
            'applicability'       => DiscountApplicability::class,
            'trigger'             => DiscountTrigger::class,
            'status'              => DiscountStatus::class,
            'value'               => 'decimal:6',
            'max_discount_amount' => 'decimal:6',
            'valid_from'          => 'immutable_datetime',
            'valid_until'         => 'immutable_datetime',
            'max_uses'            => 'integer',
            'max_uses_per_tenant' => 'integer',
            'uses_count'          => 'integer',
            'auto_email'          => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $discount): void {
            if (empty($discount->id)) {
                $discount->id = (string) Str::ulid();
            }
            if (empty($discount->code)) {
                $discount->code = strtoupper(Str::random(8));
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    /** Platform admin who created this discount */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Currency for fixed-amount discounts */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /** Tenants this discount is restricted to (when applicability = specific) */
    public function assignedTenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'discount_tenant_assignments', 'discount_id', 'tenant_id')
            ->withTimestamps();
    }

    /** Usage history — immutable once written */
    public function usages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }

    /** Subscriptions that used this discount */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Only usable discounts (active + within valid window + not depleted) */
    public function scopeUsable($query)
    {
        return $query
            ->where('status', DiscountStatus::Active->value)
            ->where(fn ($q) => $q
                ->whereNull('valid_from')
                ->orWhere('valid_from', '<=', now()),
            )
            ->where(fn ($q) => $q
                ->whereNull('valid_until')
                ->orWhere('valid_until', '>=', now()),
            );
    }

    public function scopeForTenant($query, int $tenantId, string $plan)
    {
        return $query->where(fn ($q) => $q
            ->where('applicability', DiscountApplicability::All->value)
            ->orWhere(fn ($q2) => $q2
                ->where('applicability', DiscountApplicability::Specific->value)
                ->whereHas('assignedTenants', fn ($q3) => $q3->where('tenant_id', $tenantId)),
            )
            ->orWhere(fn ($q2) => $q2
                ->where('applicability', DiscountApplicability::Plan->value)
                ->where(fn ($q3) => $q3->whereRaw("FIND_IN_SET(?, applicable_plans)", [$plan])),
            ),
        );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Calculate the discount amount for a given base amount.
     * Returns DECIMAL(20,6) compatible string — caller uses Money::fromDecimal().
     */
    public function calculateAmount(string $baseAmount): string
    {
        if ($this->type === DiscountType::Fixed) {
            // Fixed discount cannot exceed the base amount
            return (string) min((float) $this->value, (float) $baseAmount);
        }

        // Percentage discount
        $calculated = bcdiv(
            bcmul((string) $baseAmount, (string) $this->value, 8),
            '100',
            8,
        );

        if ($this->max_discount_amount !== null) {
            $calculated = (string) min((float) $calculated, (float) $this->max_discount_amount);
        }

        return $calculated;
    }

    /** Whether this discount has reached its global usage limit */
    public function isDepletedForGlobal(): bool
    {
        return $this->max_uses !== null && $this->uses_count >= $this->max_uses;
    }

    /** Whether a specific tenant has exhausted their per-tenant usage limit */
    public function isDepletedForTenant(int $tenantId): bool
    {
        return $this->usages()
            ->where('tenant_id', $tenantId)
            ->count() >= $this->max_uses_per_tenant;
    }

    public function isExpired(): bool
    {
        return $this->valid_until !== null && $this->valid_until->isPast();
    }

    /** Return list of plan codes from the applicable_plans CSV field */
    public function applicablePlanList(): array
    {
        return $this->applicable_plans
            ? array_map('trim', explode(',', $this->applicable_plans))
            : [];
    }
}
