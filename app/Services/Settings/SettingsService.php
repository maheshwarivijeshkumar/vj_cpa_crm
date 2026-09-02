<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Models\OfficeSetting;
use App\Models\SystemSetting;
use App\Models\TenantSetting;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Cache;

/**
 * SettingsService — hierarchical settings resolver.
 *
 * Resolution order (highest → lowest priority):
 *   1. User Preference
 *   2. Office Setting
 *   3. Tenant Setting
 *   4. System Setting (platform)
 *   5. $default (fallback)
 *
 * Cache strategy:
 *   - Platform settings: 1 hour TTL, cleared on update
 *   - Tenant settings:   1 hour TTL per tenant, cleared on update
 *   - Office settings:   1 hour TTL per office, cleared on update
 *   - User preferences:  not cached (per-request, low volume)
 */
final class SettingsService
{
    private const CACHE_PLATFORM = 'settings:platform';
    private const CACHE_TENANT   = 'settings:tenant:%d';
    private const CACHE_OFFICE   = 'settings:office:%d';
    private const CACHE_TTL      = 3600; // 1 hour

    /**
     * Resolve a setting value through the full hierarchy.
     *
     * @param  string    $key       Dot-notation: "group.key" e.g. "invoice.prefix"
     * @param  mixed     $default   Fallback when nothing found
     * @param  int|null  $tenantId  Tenant scope (null = platform admin)
     * @param  int|null  $officeId  Office scope
     * @param  int|null  $userId    User scope
     */
    public static function get(
        string $key,
        mixed  $default   = null,
        ?int   $tenantId  = null,
        ?int   $officeId  = null,
        ?int   $userId    = null,
    ): mixed {
        [$group, $settingKey] = self::parseKey($key);

        if ($userId !== null) {
            $value = self::getUserPreference($userId, $key);
            if ($value !== null) return $value;
        }

        if ($officeId !== null) {
            $value = self::getOfficeSetting($officeId, $group, $settingKey);
            if ($value !== null) return $value;
        }

        if ($tenantId !== null) {
            $value = self::getTenantSetting($tenantId, $group, $settingKey);
            if ($value !== null) return $value;
        }

        $value = self::getPlatformSetting($group, $settingKey);

        return $value ?? $default;
    }

    /**
     * Resolve a setting for the currently authenticated user (auto-context).
     */
    public static function forCurrentUser(string $key, mixed $default = null): mixed
    {
        $user = auth()->user();

        if ($user === null) {
            return self::get($key, $default);
        }

        return self::get($key, $default, $user->tenant_id, $user->office_id, $user->id);
    }

    // ── Setters ───────────────────────────────────────────────────────────────

    public static function setPlatform(string $key, mixed $value, string $type = 'string'): void
    {
        [$group, $settingKey] = self::parseKey($key);

        SystemSetting::updateOrCreate(
            ['group' => $group, 'key' => $settingKey],
            ['value' => self::serialize($value), 'type' => $type],
        );

        Cache::forget(self::CACHE_PLATFORM);
    }

    public static function setForTenant(int $tenantId, string $key, mixed $value, string $type = 'string'): void
    {
        [$group, $settingKey] = self::parseKey($key);

        TenantSetting::updateOrCreate(
            ['tenant_id' => $tenantId, 'group' => $group, 'key' => $settingKey],
            ['value' => self::serialize($value), 'type' => $type],
        );

        Cache::forget(sprintf(self::CACHE_TENANT, $tenantId));
    }

    public static function setForOffice(int $officeId, string $key, mixed $value, string $type = 'string'): void
    {
        [$group, $settingKey] = self::parseKey($key);

        OfficeSetting::updateOrCreate(
            ['office_id' => $officeId, 'group' => $group, 'key' => $settingKey],
            ['value' => self::serialize($value), 'type' => $type],
        );

        Cache::forget(sprintf(self::CACHE_OFFICE, $officeId));
    }

    public static function setForUser(int $userId, string $key, mixed $value, string $type = 'string'): void
    {
        UserPreference::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => self::serialize($value), 'type' => $type],
        );
    }

    // ── Cache clearers ────────────────────────────────────────────────────────

    public static function clearPlatformCache(): void
    {
        Cache::forget(self::CACHE_PLATFORM);
    }

    public static function clearTenantCache(int $tenantId): void
    {
        Cache::forget(sprintf(self::CACHE_TENANT, $tenantId));
    }

    public static function clearOfficeCache(int $officeId): void
    {
        Cache::forget(sprintf(self::CACHE_OFFICE, $officeId));
    }

    // ── Private cache readers ─────────────────────────────────────────────────

    private static function getPlatformSetting(string $group, string $key): mixed
    {
        $all = Cache::remember(self::CACHE_PLATFORM, self::CACHE_TTL, fn (): array =>
            SystemSetting::all()
                ->keyBy(fn ($s) => "{$s->group}.{$s->key}")
                ->map(fn ($s) => ['value' => $s->value, 'type' => $s->type])
                ->toArray(),
        );

        $entry = $all["{$group}.{$key}"] ?? null;

        return $entry !== null ? self::cast($entry['value'], $entry['type']) : null;
    }

    private static function getTenantSetting(int $tenantId, string $group, string $key): mixed
    {
        $all = Cache::remember(
            sprintf(self::CACHE_TENANT, $tenantId),
            self::CACHE_TTL,
            fn (): array => TenantSetting::where('tenant_id', $tenantId)
                ->get()
                ->keyBy(fn ($s) => "{$s->group}.{$s->key}")
                ->map(fn ($s) => ['value' => $s->value, 'type' => $s->type])
                ->toArray(),
        );

        $entry = $all["{$group}.{$key}"] ?? null;

        return $entry !== null ? self::cast($entry['value'], $entry['type']) : null;
    }

    private static function getOfficeSetting(int $officeId, string $group, string $key): mixed
    {
        $all = Cache::remember(
            sprintf(self::CACHE_OFFICE, $officeId),
            self::CACHE_TTL,
            fn (): array => OfficeSetting::where('office_id', $officeId)
                ->get()
                ->keyBy(fn ($s) => "{$s->group}.{$s->key}")
                ->map(fn ($s) => ['value' => $s->value, 'type' => $s->type])
                ->toArray(),
        );

        $entry = $all["{$group}.{$key}"] ?? null;

        return $entry !== null ? self::cast($entry['value'], $entry['type']) : null;
    }

    private static function getUserPreference(int $userId, string $key): mixed
    {
        $pref = UserPreference::where('user_id', $userId)->where('key', $key)->first();

        return $pref !== null ? self::cast($pref->value, $pref->type) : null;
    }

    private static function parseKey(string $key): array
    {
        if (str_contains($key, '.')) {
            [$group, $settingKey] = explode('.', $key, 2);
            return [$group, $settingKey];
        }

        return ['general', $key];
    }

    private static function cast(mixed $value, string $type): mixed
    {
        if ($value === null) return null;

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float'   => (float) $value,
            'json'    => json_decode((string) $value, true),
            default   => $value,
        };
    }

    private static function serialize(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return (string) json_encode($value);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        return (string) $value;
    }
}
