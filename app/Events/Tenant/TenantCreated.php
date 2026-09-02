<?php

declare(strict_types=1);

namespace App\Events\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired after a new tenant (accounting firm) is created.
 * Listeners: SeedTenantRoles, SeedTenantModules.
 * Always dispatched via DB::afterCommit() to prevent pre-rollback seeds.
 */
final class TenantCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly User   $owner,  // The firm owner user
    ) {}
}
