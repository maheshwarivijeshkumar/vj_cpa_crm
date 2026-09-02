<?php

namespace App\Models;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Timezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $slug
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $website
 * @property string $timezone
 * @property string $locale
 * @property string $currency
 * @property string $fiscal_year_start
 * @property string $plan
 * @property string $status
 * @property bool $is_active
 * @property array|null $brand_colors
 * @property array|null $settings
 * @property array|null $metadata
 */
class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'slug', 'email', 'phone', 'website',
        'address_line1', 'address_line2', 'city', 'state', 'postal_code',
        'country_id', 'timezone_id', 'language_id', 'currency_id',
        'fiscal_year_start_month', 'fiscal_year_start_day',
        'logo_path', 'brand_colors', 'plan', 'status',
        'trial_ends_at', 'suspended_at', 'settings', 'metadata', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'brand_colors'               => 'array',
            'settings'                   => 'array',
            'metadata'                   => 'array',
            'is_active'                  => 'boolean',
            'trial_ends_at'              => 'datetime',
            'suspended_at'               => 'datetime',
            'fiscal_year_start_month'    => 'integer',
            'fiscal_year_start_day'      => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $tenant): void {
            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) Str::uuid();
            }
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function featureFlags(): HasMany
    {
        return $this->hasMany(FeatureFlag::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeOnPlan($query, string $plan)
    {
        return $query->where('plan', $plan);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at?->isFuture();
    }
}
