<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeds platform-level and demo tenant users with proper role assignments.
 *
 * Platform users  (tenant_id = null)
 * ─────────────────────────────────
 *  administrator@cpacrm.com  │ Platform Super Admin  │ platform-super-admin role
 *  admin@cpacrm.com          │ Platform Admin        │ platform-super-admin role
 *  support@cpacrm.com        │ Platform Support      │ platform-support role
 *
 * Demo Tenant: "Kambo & Associates CPA"
 * ──────────────────────────────────────
 *  owner@demo.cpacrm.com     │ Firm Owner            │ firm-owner role
 *  manager@demo.cpacrm.com   │ Partner/Manager       │ partner-manager role
 *  accountant@demo.cpacrm.com│ Senior Accountant     │ senior-accountant role
 *  staff@demo.cpacrm.com     │ Staff                 │ staff role
 *  client@demo.cpacrm.com    │ Client Portal User    │ client-portal-user role
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── 1. Resolve needed role IDs ────────────────────────────────────────
        $roles = DB::table('roles')
            ->whereNull('tenant_id')
            ->pluck('id', 'slug');

        // ── 2. Resolve a country & timezone for demo data (Canada / Toronto) ──
        $canadaId = DB::table('countries')->where('iso2', 'CA')->value('id');
        $torontoTzId = DB::table('timezones')
            ->where('zone_name', 'America/Toronto')
            ->value('id');
        $cadCurrencyId = DB::table('currencies')
            ->where('currency', 'CAD')
            ->value('id');
        $englishId = DB::table('languages')
            ->where('code', 'en')
            ->value('id');

        // ── 3. Platform users ─────────────────────────────────────────────────
        $platformUsers = [
            [
                'first_name' => 'Platform',
                'last_name'  => 'Administrator',
                'email'      => env('INITIAL_ADMIN_EMAIL', 'administrator@cpacrm.com'),
                'username'   => 'administrator',
                'password'   => env('INITIAL_ADMIN_PASSWORD', 'administrator90@#$'),
                'user_type'  => 'platform_admin',
                'role_slug'  => 'platform-super-admin',
                'must_change_password' => false,
            ],
            [
                'first_name' => 'Platform',
                'last_name'  => 'Admin',
                'email'      => 'admin@cpacrm.com',
                'username'   => 'platform.admin',
                'password'   => 'Admin@CPA2026!',
                'user_type'  => 'platform_admin',
                'role_slug'  => 'platform-super-admin',
                'must_change_password' => true,
            ],
            [
                'first_name' => 'Platform',
                'last_name'  => 'Support',
                'email'      => 'support@cpacrm.com',
                'username'   => 'platform.support',
                'password'   => 'Support@CPA2026!',
                'user_type'  => 'platform_admin',
                'role_slug'  => 'platform-support',
                'must_change_password' => true,
            ],
        ];

        foreach ($platformUsers as $data) {
            $userId = $this->upsertUser(array_merge($data, [
                'tenant_id'   => null,
                'office_id'   => null,
                'timezone_id' => $torontoTzId,
                'language_id' => $englishId,
                'currency_id' => $cadCurrencyId,
                'now'         => $now,
            ]));

            $this->assignRole($userId, $roles[$data['role_slug']] ?? null, $now);
        }

        $this->command->info('Platform users seeded.');

        // ── 4. Demo tenant ────────────────────────────────────────────────────
        $tenantId = DB::table('tenants')->where('slug', 'kambo-associates-cpa')->value('id');

        if (! $tenantId) {
            $tenantId = DB::table('tenants')->insertGetId([
                'uuid'                    => (string) Str::uuid(),
                'name'                    => 'Kambo & Associates CPA',
                'slug'                    => 'kambo-associates-cpa',
                'email'                   => 'info@kamboassociates.ca',
                'phone'                   => '+1-416-555-0100',
                'address_line1'           => '100 King Street West',
                'city'                    => 'Toronto',
                'state'                   => 'Ontario',
                'postal_code'             => 'M5X 1A1',
                'country_id'              => $canadaId,
                'timezone_id'             => $torontoTzId,
                'language_id'             => $englishId,
                'currency_id'             => $cadCurrencyId,
                'fiscal_year_start_month' => 1,
                'fiscal_year_start_day'   => 1,
                'plan'                    => 'professional',
                'status'                  => 'active',
                'is_active'               => true,
                'created_at'              => $now,
                'updated_at'              => $now,
            ]);

            $this->command->info("Demo tenant created (id: {$tenantId}).");
        }

        // ── 5. Demo tenant users ──────────────────────────────────────────────
        $tenantUsers = [
            [
                'first_name' => 'Gagan',
                'last_name'  => 'Kambo',
                'email'      => 'owner@demo.cpacrm.com',
                'username'   => 'gagan.kambo',
                'password'   => 'Owner@Demo2026!',
                'user_type'  => 'firm_owner',
                'role_slug'  => 'firm-owner',
                'must_change_password' => false,
            ],
            [
                'first_name' => 'Tom',
                'last_name'  => 'Bradley',
                'email'      => 'manager@demo.cpacrm.com',
                'username'   => 'tom.bradley',
                'password'   => 'Manager@Demo2026!',
                'user_type'  => 'firm_user',
                'role_slug'  => 'partner-manager',
                'must_change_password' => false,
            ],
            [
                'first_name' => 'Mike',
                'last_name'  => 'Chen',
                'email'      => 'accountant@demo.cpacrm.com',
                'username'   => 'mike.chen',
                'password'   => 'Accountant@Demo2026!',
                'user_type'  => 'firm_user',
                'role_slug'  => 'senior-accountant',
                'must_change_password' => false,
            ],
            [
                'first_name' => 'Sarah',
                'last_name'  => 'Wilson',
                'email'      => 'staff@demo.cpacrm.com',
                'username'   => 'sarah.wilson',
                'password'   => 'Staff@Demo2026!',
                'user_type'  => 'firm_user',
                'role_slug'  => 'staff',
                'must_change_password' => false,
            ],
            [
                'first_name' => 'Demo',
                'last_name'  => 'Client',
                'email'      => 'client@demo.cpacrm.com',
                'username'   => 'demo.client',
                'password'   => 'Client@Demo2026!',
                'user_type'  => 'client',
                'role_slug'  => 'client-portal-user',
                'must_change_password' => false,
            ],
        ];

        foreach ($tenantUsers as $data) {
            $userId = $this->upsertUser(array_merge($data, [
                'tenant_id'   => $tenantId,
                'office_id'   => null,
                'timezone_id' => $torontoTzId,
                'language_id' => $englishId,
                'currency_id' => $cadCurrencyId,
                'now'         => $now,
            ]));

            $this->assignRole($userId, $roles[$data['role_slug']] ?? null, $now);
        }

        $this->command->info('Demo tenant users seeded.');
        $this->command->newLine();
        $this->command->table(
            ['Type', 'Email', 'Password', 'Role'],
            [
                ['Platform Super Admin', 'administrator@cpacrm.com', env('INITIAL_ADMIN_PASSWORD', 'administrator90@#$'), 'platform-super-admin'],
                ['Platform Admin',       'admin@cpacrm.com',          'Admin@CPA2026!',        'platform-super-admin'],
                ['Platform Support',     'support@cpacrm.com',        'Support@CPA2026!',      'platform-support'],
                ['---', '---', '---', '---'],
                ['Firm Owner',           'owner@demo.cpacrm.com',     'Owner@Demo2026!',       'firm-owner'],
                ['Partner/Manager',      'manager@demo.cpacrm.com',   'Manager@Demo2026!',     'partner-manager'],
                ['Senior Accountant',    'accountant@demo.cpacrm.com','Accountant@Demo2026!',  'senior-accountant'],
                ['Staff',                'staff@demo.cpacrm.com',     'Staff@Demo2026!',       'staff'],
                ['Client Portal',        'client@demo.cpacrm.com',    'Client@Demo2026!',      'client-portal-user'],
            ]
        );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Insert or update a user record. Returns the user ID.
     */
    private function upsertUser(array $data): int
    {
        $existing = DB::table('users')->where('email', $data['email'])->first();

        $payload = [
            'tenant_id'            => $data['tenant_id'],
            'office_id'            => $data['office_id'],
            'username'             => $data['username'],
            'first_name'           => $data['first_name'],
            'last_name'            => $data['last_name'],
            'email'                => $data['email'],
            'password'             => Hash::make($data['password']),
            'email_verified_at'    => $data['now'],
            'user_type'            => $data['user_type'],
            'status'               => 'active',
            'must_change_password' => $data['must_change_password'],
            'two_factor_enabled'   => false,
            'timezone_id'          => $data['timezone_id'],
            'language_id'          => $data['language_id'],
            'currency_id'          => $data['currency_id'],
            'date_format'          => 'MM/DD/YYYY',
            'updated_at'           => $data['now'],
        ];

        if ($existing) {
            DB::table('users')->where('id', $existing->id)->update($payload);
            return $existing->id;
        }

        return DB::table('users')->insertGetId(array_merge($payload, [
            'uuid'       => (string) Str::uuid(),
            'created_at' => $data['now'],
        ]));
    }

    /**
     * Assign a role to a user (idempotent).
     */
    private function assignRole(int $userId, ?int $roleId, mixed $now): void
    {
        if ($roleId === null) {
            return;
        }

        DB::table('role_user')->updateOrInsert(
            ['user_id' => $userId, 'role_id' => $roleId],
            ['created_at' => $now, 'updated_at' => $now],
        );
    }
}
