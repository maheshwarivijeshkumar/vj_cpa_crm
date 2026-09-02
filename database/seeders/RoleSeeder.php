<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── Platform Roles (tenant_id = null) ─────────────────────────────────
        $platformRoles = [
            [
                'slug'        => 'platform-super-admin',
                'name'        => 'Platform Super Admin',
                'description' => 'Full unrestricted access to the entire platform.',
                'sort_order'  => 1,
            ],
            [
                'slug'        => 'platform-admin',
                'name'        => 'Platform Admin',
                'description' => 'Full unrestricted access to the entire platform.',
                'sort_order'  => 2,
            ],
            [
                'slug'        => 'platform-support',
                'name'        => 'Platform Support',
                'description' => 'Read-only access for support staff.',
                'sort_order'  => 3,
            ],
        ];

        foreach ($platformRoles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug'], 'tenant_id' => null],
                array_merge($role, [
                    'uuid'       => (string) Str::uuid(),
                    'tenant_id'  => null,
                    'guard'      => 'web',
                    'is_system'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        // ── Firm Roles (template roles — no tenant_id, is_system = true) ──────
        // These are copied to each new tenant on registration.
        $firmRoles = [
            [
                'slug'        => 'firm-owner',
                'name'        => 'Firm Owner',
                'description' => 'Full access to the firm account. Cannot be deleted.',
                'sort_order'  => 10,
                'permissions' => '*', // all permissions (handled in PlatformAdministratorSeeder and firm creation)
            ],
            [
                'slug'        => 'partner-manager',
                'name'        => 'Partner / Manager',
                'description' => 'Senior staff with full operational access. Cannot manage billing/settings.',
                'sort_order'  => 20,
                'permissions' => 'operational',
            ],
            [
                'slug'        => 'senior-accountant',
                'name'        => 'Senior Accountant',
                'description' => 'Full accounting and client work access.',
                'sort_order'  => 30,
                'permissions' => 'senior_accountant',
            ],
            [
                'slug'        => 'accountant',
                'name'        => 'Accountant',
                'description' => 'Standard accountant access to assigned clients and tasks.',
                'sort_order'  => 40,
                'permissions' => 'accountant',
            ],
            [
                'slug'        => 'bookkeeper',
                'name'        => 'Bookkeeper',
                'description' => 'Bookkeeping and basic accounting access.',
                'sort_order'  => 50,
                'permissions' => 'bookkeeper',
            ],
            [
                'slug'        => 'staff',
                'name'        => 'Staff',
                'description' => 'Basic staff access — tasks, documents, calendar.',
                'sort_order'  => 60,
                'permissions' => 'staff',
            ],
            [
                'slug'        => 'client-portal-user',
                'name'        => 'Client Portal User',
                'description' => 'Limited portal-only access for clients.',
                'sort_order'  => 90,
                'permissions' => 'client_portal',
            ],
        ];

        foreach ($firmRoles as $role) {
            $permissions = $role['permissions'];
            unset($role['permissions']);

            $roleId = DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug'], 'tenant_id' => null],
                array_merge($role, [
                    'uuid'       => (string) Str::uuid(),
                    'tenant_id'  => null,
                    'guard'      => 'web',
                    'is_system'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        // ── Assign all permissions to Platform Super Admin ────────────────────
        $superAdminRole = DB::table('roles')
            ->where('slug', 'platform-super-admin')
            ->where('tenant_id', null)
            ->first();

        if ($superAdminRole) {
            $allPermissions = DB::table('permissions')->pluck('id');
            $existing = DB::table('permission_role')
                ->where('role_id', $superAdminRole->id)
                ->pluck('permission_id')
                ->toArray();

            $toInsert = $allPermissions
                ->reject(fn ($id) => in_array($id, $existing))
                ->map(fn ($id) => [
                    'permission_id' => $id,
                    'role_id'       => $superAdminRole->id,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ])
                ->values()
                ->toArray();

            foreach (array_chunk($toInsert, 100) as $chunk) {
                DB::table('permission_role')->insert($chunk);
            }
        }

        // ── Assign permissions to Firm Owner role ─────────────────────────────
        $firmOwnerRole = DB::table('roles')
            ->where('slug', 'firm-owner')
            ->where('tenant_id', null)
            ->first();

        if ($firmOwnerRole) {
            // Firm owner gets all non-platform permissions
            $firmPermissions = DB::table('permissions')
                ->where('module', '!=', 'platform')
                ->pluck('id');

            $existing = DB::table('permission_role')
                ->where('role_id', $firmOwnerRole->id)
                ->pluck('permission_id')
                ->toArray();

            $toInsert = $firmPermissions
                ->reject(fn ($id) => in_array($id, $existing))
                ->map(fn ($id) => [
                    'permission_id' => $id,
                    'role_id'       => $firmOwnerRole->id,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ])
                ->values()
                ->toArray();

            foreach (array_chunk($toInsert, 100) as $chunk) {
                DB::table('permission_role')->insert($chunk);
            }
        }
    }
}
