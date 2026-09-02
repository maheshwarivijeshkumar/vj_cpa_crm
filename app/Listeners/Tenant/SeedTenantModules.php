<?php

declare(strict_types=1);

namespace App\Listeners\Tenant;

use App\Events\Tenant\TenantCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

/**
 * Activates all core modules for a newly created tenant.
 * Non-core modules default to disabled and can be enabled per-plan.
 */
final class SeedTenantModules implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(TenantCreated $event): void
    {
        $now     = now();
        $modules = DB::table('modules')
            ->where('is_enabled', true)
            ->get(['id', 'is_core']);

        foreach ($modules as $module) {
            DB::table('tenant_modules')->updateOrInsert(
                ['tenant_id' => $event->tenant->id, 'module_id' => $module->id],
                [
                    'is_enabled' => (bool) $module->is_core,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
