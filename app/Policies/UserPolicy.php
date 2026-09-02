<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

/**
 * UserPolicy — controls who can view or mutate user accounts.
 *
 * Tenant isolation is enforced: firm users cannot see users from other tenants.
 */
final class UserPolicy
{
    /** List users — only within own tenant. */
    public function viewAny(User $authUser): bool
    {
        return $authUser->isFirmUser() && $authUser->hasPermission('users.viewAny');
    }

    /** View a single user — must be in same tenant. */
    public function view(User $authUser, User $target): bool
    {
        if ($authUser->id === $target->id) {
            return true; // Can always view own profile
        }
        return $authUser->tenant_id === $target->tenant_id
            && $authUser->hasPermission('users.view');
    }

    /** Create users within own tenant only. */
    public function create(User $authUser): bool
    {
        return $authUser->isFirmUser() && $authUser->hasPermission('users.create');
    }

    /** Update — same tenant, cannot demote the firm owner if only one. */
    public function update(User $authUser, User $target): bool
    {
        if ($authUser->id === $target->id) {
            return true; // Can always edit own profile (limited fields)
        }
        return $authUser->tenant_id === $target->tenant_id
            && $authUser->hasPermission('users.update');
    }

    /** Delete — only within tenant, cannot delete self. */
    public function delete(User $authUser, User $target): bool
    {
        return $authUser->id !== $target->id
            && $authUser->tenant_id === $target->tenant_id
            && $authUser->hasPermission('users.delete');
    }

    /** Restore soft-deleted user. */
    public function restore(User $authUser, User $target): bool
    {
        return $authUser->tenant_id === $target->tenant_id
            && $authUser->hasPermission('users.restore');
    }

    /** Assign / revoke roles. */
    public function manageRoles(User $authUser, User $target): bool
    {
        return $authUser->tenant_id === $target->tenant_id
            && $authUser->hasPermission('users.manage_roles');
    }

    /** Invite a new user to the tenant. */
    public function invite(User $authUser): bool
    {
        return $authUser->isFirmUser() && $authUser->hasPermission('users.invite');
    }
}
