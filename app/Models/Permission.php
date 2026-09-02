<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name        e.g. "clients.create"
 * @property string $guard
 * @property string $module      e.g. "clients"
 * @property string $action      e.g. "create"
 * @property bool $is_system
 */
class Permission extends Model
{
    protected $fillable = [
        'uuid', 'name', 'guard', 'module', 'action', 'description', 'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $permission): void {
            if (empty($permission->uuid)) {
                $permission->uuid = (string) Str::uuid();
            }
            // Auto-derive module + action from name "module.action"
            if (str_contains($permission->name, '.')) {
                [$module, $action] = explode('.', $permission->name, 2);
                $permission->module = $module;
                $permission->action = $action;
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user')
            ->withPivot('granted')
            ->withTimestamps();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }
}
