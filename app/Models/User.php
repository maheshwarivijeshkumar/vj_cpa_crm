<?php

namespace App\Models;

use App\Notifications\PasswordResetNotification;
use App\Notifications\VerifyEmailNotification;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Timezone;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int|null $tenant_id
 * @property int|null $office_id
 * @property string|null $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $avatar_path
 * @property string $user_type       platform_admin|firm_owner|firm_user|client
 * @property string $status          active|inactive|suspended|invited|archived
 * @property bool $must_change_password
 * @property bool $two_factor_enabled
 * @property string|null $timezone
 * @property string|null $locale
 * @property string|null $currency
 * @property Carbon|null $last_login_at
 * @property Carbon|null $email_verified_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid', 'tenant_id', 'office_id', 'username',
        'first_name', 'last_name', 'email', 'phone', 'avatar_path',
        'password', 'user_type', 'status', 'must_change_password',
        'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes',
        'two_factor_confirmed_at', 'last_login_at', 'last_login_ip',
        'timezone_id', 'language_id', 'currency_id',
        'date_format', 'number_format', 'preferences',
        'email_verified_at', 'invited_at', 'archived_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'two_factor_confirmed_at'  => 'datetime',
            'last_login_at'            => 'datetime',
            'invited_at'               => 'datetime',
            'archived_at'              => 'datetime',
            'must_change_password'     => 'boolean',
            'two_factor_enabled'       => 'boolean',
            'preferences'              => 'array',
            'password'                 => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $user): void {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    // ─── Custom notifications ─────────────────────────────────────────────────

    /**
     * Send the password reset notification using our branded mail.
     */
    public function sendPasswordResetNotification(mixed $token): void
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * Send the email verification notification using our branded mail.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    // ─── Computed ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->email;
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withPivot('granted')
            ->withTimestamps();
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Permission Helpers ───────────────────────────────────────────────────

    /**
     * Check if the user has a given CPA permission string (via roles or direct grants).
     * Platform admins always return true.
     *
     * Named hasPermission() to avoid conflicting with Laravel's Gate can() method.
     * Use $user->hasPermission('clients.create') in PHP.
     * Frontend uses the can() composable from the auth store.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isPlatformAdmin()) {
            return true;
        }

        // Check direct deny
        $direct = $this->directPermissions->firstWhere('name', $permission);
        if ($direct && ! $direct->pivot->granted) {
            return false;
        }

        // Check direct grant
        if ($direct && $direct->pivot->granted) {
            return true;
        }

        // Check via roles
        return $this->roles->contains(
            fn (Role $role) => $role->hasPermission($permission)
        );
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles->whereIn('slug', $slugs)->isNotEmpty();
    }

    // ─── Type Helpers ─────────────────────────────────────────────────────────

    public function isPlatformAdmin(): bool
    {
        return $this->user_type === 'platform_admin';
    }

    public function isFirmUser(): bool
    {
        return in_array($this->user_type, ['firm_owner', 'firm_user'], true);
    }

    public function isClient(): bool
    {
        return $this->user_type === 'client';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePlatformAdmins($query)
    {
        return $query->where('user_type', 'platform_admin');
    }

    public function scopeFirmUsers($query)
    {
        return $query->whereIn('user_type', ['firm_owner', 'firm_user']);
    }
}
