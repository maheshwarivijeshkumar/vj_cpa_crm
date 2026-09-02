<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Immutable — no updated_at, no soft delete, no direct writes via UI.
 * Use AuditService to create entries.
 *
 * @property int $id
 * @property string $uuid
 * @property int|null $tenant_id
 * @property int|null $user_id
 * @property string|null $user_name
 * @property string $event_type
 * @property string|null $module
 * @property string|null $resource_type
 * @property string|null $resource_id
 * @property string|null $resource_label
 * @property string|null $description
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array|null $metadata
 */
class AuditLog extends Model
{
    // No updated_at — this is an append-only log
    const UPDATED_AT = null;

    protected $fillable = [
        'uuid', 'tenant_id', 'user_id', 'user_name', 'event_type',
        'module', 'resource_type', 'resource_id', 'resource_label',
        'description', 'old_values', 'new_values',
        'ip_address', 'user_agent', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata'   => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $log): void {
            if (empty($log->uuid)) {
                $log->uuid = (string) Str::uuid();
            }
        });

        // Prevent updates and deletes — audit logs are immutable
        static::updating(static fn () => false);
        static::deleting(static fn () => false);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForResource($query, string $type, string $id)
    {
        return $query->where('resource_type', $type)->where('resource_id', $id);
    }

    public function scopeEventType($query, string $type)
    {
        return $query->where('event_type', $type);
    }
}
