<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property int $country_id
 * @property string $zone_name
 * @property int $gmt_offset
 * @property string $gmt_offset_name
 * @property string|null $abbreviation
 * @property string|null $tz_name
 * @property bool $is_active
 */
class Timezone extends Model
{
    protected $fillable = [
        'uuid', 'country_id', 'zone_name', 'gmt_offset',
        'gmt_offset_name', 'abbreviation', 'tz_name', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gmt_offset' => 'integer',
            'is_active'  => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
