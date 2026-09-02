<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property bool $is_core
 * @property bool $is_enabled
 * @property array|null $dependencies
 * @property int $sort_order
 * @property array|null $settings
 */
class Module extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'is_core', 'is_enabled',
        'dependencies', 'sort_order', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_core'      => 'boolean',
            'is_enabled'   => 'boolean',
            'dependencies' => 'array',
            'settings'     => 'array',
            'sort_order'   => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }
}
