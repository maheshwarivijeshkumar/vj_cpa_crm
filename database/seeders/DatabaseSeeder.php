<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed order respects FK dependencies strictly:
     *
     *  1. countries          (no deps)
     *  2. currencies         (→ countries)
     *  3. timezones          (→ countries)
     *  4. languages          (no deps)
     *  5. system_settings    (no deps)
     *  6. modules            (no deps)
     *  7. permissions        (no deps)
     *  8. roles              (→ permissions, for pivot)
     *  9. platform admin     (→ users, roles)
     */
    public function run(): void
    {
        // ── Reference data ────────────────────────────────────────────────────
        $this->call(CountrySeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(TimezoneSeeder::class);
        $this->call(LanguageSeeder::class);

        // ── Platform config ───────────────────────────────────────────────────
        $this->call(SystemSettingsSeeder::class);
        $this->call(ModuleSeeder::class);

        // ── RBAC ──────────────────────────────────────────────────────────────
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        // ── Bootstrap admin ───────────────────────────────────────────────────
        $this->call(PlatformAdministratorSeeder::class);

        // ── Extended user set (platform team + demo tenant) ───────────────────
        $this->call(UserSeeder::class);

        // ── SEO meta defaults ─────────────────────────────────────────────────
        $this->call(SeoMetaSeeder::class);

        // ── Blog content ──────────────────────────────────────────────────────
        $this->call(BlogSeeder::class);

        // ── Notification templates ────────────────────────────────────────────
        $this->call(NotificationTemplateSeeder::class);
    }
}
