<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

/**
 * TenantPolicy — controls who can manage accounting firm (tenant) records.
 *
 * Platform admins bypass all policies via Gate::before() in AppServiceProvider.
 * Firm owners can only manage their own tenant.
 */
final class TenantPolicy
{
    /** Any authenticated firm user can view their own tenant. */
    public function view(User $user, Tenant $tenant): bool
    {
        return $user->tenant_id === $tenant->id;
    }

    /** Only platform admins (bypassed via Gate::before) or no firm-level view. */
    public function viewAny(User $user): bool
    {
        return false; // Only platform admins; Gate::before returns true for them
    }

    /** Only firm owners can update their firm's settings. */
    public function update(User $user, Tenant $tenant): bool
    {
        return $user->tenant_id === $tenant->id
            && $user->user_type === 'firm_owner';
    }

    /** Only platform admins can create tenants (bypassed via Gate::before). */
    public function create(User $user): bool
    {
        return false;
    }

    /** Only platform admins can delete tenants. */
    public function delete(User $user, Tenant $tenant): bool
    {
        return false;
    }

    /** Only platform admins can restore tenants. */
    public function restore(User $user, Tenant $tenant): bool
    {
        return false;
    }

    /** Firm owners can manage their own billing/subscription. */
    public function manageBilling(User $user, Tenant $tenant): bool
    {
        return $user->tenant_id === $tenant->id
            && $user->user_type === 'firm_owner';
    }

    /** Firm owners can manage their team. */
    public function manageTeam(User $user, Tenant $tenant): bool
    {
        return $user->tenant_id === $tenant->id
            && in_array($user->user_type, ['firm_owner', 'firm_user'], true)
            && $user->hasPermission('users.viewAny');
    }
}
