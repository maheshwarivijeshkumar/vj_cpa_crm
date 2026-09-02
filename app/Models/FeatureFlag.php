<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $tenant_id  null = platform-level flag
 * @property string $key
 * @property bool $value
 */
class FeatureFlag extends Model
{
    protected $fillable = ['tenant_id', 'key', 'value', 'description'];

    protected function casts(): array
    {
        return [
            'value' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePlatformLevel($query)
    {
        return $query->whereNull('tenant_id');
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeEnabled($query)
    {
        return $query->where('value', true);
    }
}
