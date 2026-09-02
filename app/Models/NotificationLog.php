<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * NotificationLog — immutable append-only delivery record.
 * Updates and deletes are blocked (audit integrity).
 */
class NotificationLog extends Model
{
    // No updated_at — append-only
    const UPDATED_AT = null;

    protected $fillable = [
        'uuid', 'tenant_id', 'user_id', 'user_name',
        'notification_template_id', 'template_key', 'channel',
        'subject', 'body', 'status', 'error_message', 'metadata',
        'is_read', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata'   => 'array',
            'is_read'    => 'boolean',
            'read_at'    => 'datetime',
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

        // Prevent updates and hard deletes — immutable log
        static::updating(static fn () => false);
        static::deleting(static fn () => false);
    }

    public function tenant(): BelongsTo  { return $this->belongsTo(Tenant::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function template(): BelongsTo { return $this->belongsTo(NotificationTemplate::class, 'notification_template_id'); }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false)->where('channel', 'in_app');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
