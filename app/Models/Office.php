<?php

namespace App\Models;

use App\Models\Country;
use App\Models\Timezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $tenant_id
 * @property string $name
 * @property string|null $code
 * @property bool $is_headquarters
 * @property bool $is_active
 * @property array|null $settings
 */
class Office extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'tenant_id', 'name', 'code', 'email', 'phone',
        'address_line1', 'address_line2', 'city', 'state', 'postal_code',
        'country_id', 'timezone_id', 'settings', 'is_headquarters', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'settings'        => 'array',
            'is_headquarters' => 'boolean',
            'is_active'       => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $office): void {
            if (empty($office->uuid)) {
                $office->uuid = (string) Str::uuid();
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(OfficeSetting::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
