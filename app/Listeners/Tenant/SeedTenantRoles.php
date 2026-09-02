<?php

declare(strict_types=1);

namespace App\Listeners\Tenant;

use App\Events\Tenant\TenantCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Copies all template firm roles (tenant_id = null, is_system = true)
 * into the new tenant's own role set.
 * Called after DB commit to ensure the tenant row exists.
 */
final class SeedTenantRoles implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(TenantCreated $event): void
    {
        $now          = now();
        $templateRoles = DB::table('roles')
            ->whereNull('tenant_id')
            ->where('is_system', true)
            ->whereNotIn('slug', ['platform-super-admin', 'platform-support'])
            ->get();

        foreach ($templateRoles as $template) {
            DB::table('roles')->updateOrInsert(
                ['tenant_id' => $event->tenant->id, 'slug' => $template->slug],
                [
                    'uuid'        => (string) Str::uuid(),
                    'tenant_id'   => $event->tenant->id,
                    'name'        => $template->name,
                    'slug'        => $template->slug,
                    'guard'       => 'web',
                    'description' => $template->description,
                    'is_system'   => true,
                    'sort_order'  => $template->sort_order,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
            );
        }

        // Assign firm-owner role to the owner user
        $firmOwnerRole = DB::table('roles')
            ->where('tenant_id', $event->tenant->id)
            ->where('slug', 'firm-owner')
            ->first();

        if ($firmOwnerRole) {
            DB::table('role_user')->updateOrInsert(
                ['user_id' => $event->owner->id, 'role_id' => $firmOwnerRole->id],
                ['created_at' => $now, 'updated_at' => $now],
            );
        }
    }
}
