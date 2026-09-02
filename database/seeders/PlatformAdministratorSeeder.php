<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlatformAdministratorSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('INITIAL_ADMIN_EMAIL', 'administrator@cpacrm.com');
        $username = env('INITIAL_ADMIN_USERNAME', 'administrator');
        $password = env('INITIAL_ADMIN_PASSWORD', 'administrator90@#$');
        $mustChange = filter_var(env('MUST_CHANGE_PASSWORD', 'false'), FILTER_VALIDATE_BOOLEAN);

        $now = now();

        // ── Create or update the platform admin user ───────────────────────────
        $existing = DB::table('users')->where('email', $email)->first();

        if ($existing) {
            // Already exists — update password only if MUST_CHANGE_PASSWORD
            if ($mustChange) {
                DB::table('users')->where('id', $existing->id)->update([
                    'must_change_password' => true,
                    'updated_at'           => $now,
                ]);
            }
            $userId = $existing->id;
        } else {
            $userId = DB::table('users')->insertGetId([
                'uuid'                 => (string) Str::uuid(),
                'tenant_id'            => null,
                'office_id'            => null,
                'username'             => $username,
                'first_name'           => 'Platform',
                'last_name'            => 'Administrator',
                'email'                => $email,
                'email_verified_at'    => $now,
                'password'             => Hash::make($password),
                'user_type'            => 'platform_admin',
                'status'               => 'active',
                'must_change_password' => $mustChange,
                'two_factor_enabled'   => false,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        // ── Assign Platform Super Admin role ──────────────────────────────────
        $superAdminRole = DB::table('roles')
            ->where('slug', 'platform-super-admin')
            ->whereNull('tenant_id')
            ->first();

        if ($superAdminRole && $userId) {
            DB::table('role_user')->updateOrInsert(
                ['user_id' => $userId, 'role_id' => $superAdminRole->id],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        $this->command->info("Platform admin ready: {$email}");
    }
}
