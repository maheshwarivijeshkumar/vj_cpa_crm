<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string|null $iso2
 * @property string|null $iso3
 * @property string|null $numeric_code
 * @property string|null $phonecode
 * @property string|null $capital
 * @property string|null $tld
 * @property string|null $native
 * @property string|null $nationality
 * @property string|null $emoji
 * @property string|null $emoji_u
 * @property bool $is_active
 */
class Country extends Model
{
    protected $fillable = [
        'uuid', 'name', 'iso2', 'iso3', 'numeric_code', 'phonecode',
        'capital', 'tld', 'native', 'nationality', 'emoji', 'emoji_u', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }

    public function timezones(): HasMany
    {
        return $this->hasMany(Timezone::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
