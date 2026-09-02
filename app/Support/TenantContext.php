<?php

namespace App\Support;

use App\Models\Tenant;
use App\Models\User;
use RuntimeException;

/**
 * Holds the current authenticated tenant context for the request lifecycle.
 *
 * Resolution order:
 *   1. Authenticated user's tenant_id
 *   2. Platform admins have no tenant (tenant_id = null)
 *
 * NEVER accept tenant_id from request input.
 */
final class TenantContext
{
    private static ?Tenant $tenant = null;
    private static bool $resolved = false;

    /**
     * Resolve and store tenant from the authenticated user.
     * Called once per request by EnsureTenantContext middleware.
     */
    public static function resolveFromUser(User $user): void
    {
        self::$resolved = true;

        if ($user->isPlatformAdmin()) {
            self::$tenant = null;
            return;
        }

        if ($user->tenant_id === null) {
            throw new RuntimeException(
                "Firm user [{$user->id}] has no tenant_id assigned."
            );
        }

        self::$tenant = $user->tenant ?? Tenant::findOrFail($user->tenant_id);
    }

    /**
     * Get the current tenant. Throws for firm users if not resolved.
     */
    public static function get(): ?Tenant
    {
        return self::$tenant;
    }

    /**
     * Get the current tenant ID. Returns null for platform admins.
     */
    public static function id(): ?int
    {
        return self::$tenant?->id;
    }

    /**
     * Assert a tenant is present (throws if platform admin tries to access tenant resource).
     */
    public static function require(): Tenant
    {
        if (self::$tenant === null) {
            throw new RuntimeException('No tenant context available for this operation.');
        }

        return self::$tenant;
    }

    /**
     * Check whether we are operating in a tenant context.
     */
    public static function hasTenant(): bool
    {
        return self::$tenant !== null;
    }

    /**
     * Check whether the context belongs to a platform admin (no tenant).
     */
    public static function isPlatformAdmin(): bool
    {
        return self::$resolved && self::$tenant === null;
    }

    /**
     * Force-set tenant (testing only).
     */
    public static function setForTesting(?Tenant $tenant): void
    {
        self::$tenant = $tenant;
        self::$resolved = true;
    }

    /**
     * Reset context (called at end of request / testing teardown).
     */
    public static function reset(): void
    {
        self::$tenant = null;
        self::$resolved = false;
    }
}
