<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uuid
 * @property string $code
 * @property string $name
 * @property string|null $native_name
 * @property bool $is_rtl
 * @property bool $is_active
 */
class Language extends Model
{
    protected $fillable = [
        'uuid', 'code', 'name', 'native_name', 'is_rtl', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_rtl'    => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
