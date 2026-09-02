<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Platform ─────────────────────────────────────────────────────
            ['group' => 'platform', 'key' => 'name',                   'value' => 'VJ CPA CRM',            'type' => 'string',  'description' => 'Platform display name',                    'is_public' => true],
            ['group' => 'platform', 'key' => 'tagline',                'value' => 'Enterprise CPA Practice Management', 'type' => 'string', 'description' => 'Platform tagline', 'is_public' => true],
            ['group' => 'platform', 'key' => 'support_email',          'value' => 'support@cpacrm.com',    'type' => 'string',  'description' => 'Support contact email',                    'is_public' => true],
            ['group' => 'platform', 'key' => 'default_timezone',       'value' => 'America/Toronto',        'type' => 'string',  'description' => 'Default platform timezone',                'is_public' => true],
            ['group' => 'platform', 'key' => 'default_language',       'value' => 'en',                     'type' => 'string',  'description' => 'Default platform language code',           'is_public' => true],
            ['group' => 'platform', 'key' => 'default_currency',       'value' => 'CAD',                    'type' => 'string',  'description' => 'Default platform currency ISO code',       'is_public' => true],
            ['group' => 'platform', 'key' => 'default_date_format',    'value' => 'MM/DD/YYYY',             'type' => 'string',  'description' => 'Default date display format',              'is_public' => true],
            ['group' => 'platform', 'key' => 'maintenance_mode',       'value' => 'false',                  'type' => 'boolean', 'description' => 'Put platform in maintenance mode',         'is_public' => false],
            ['group' => 'platform', 'key' => 'registration_open',      'value' => 'true',                   'type' => 'boolean', 'description' => 'Allow new tenant self-registration',       'is_public' => false],
            ['group' => 'platform', 'key' => 'trial_days',             'value' => '14',                     'type' => 'integer', 'description' => 'Trial period in days for new tenants',     'is_public' => true],

            // ── Security ──────────────────────────────────────────────────────
            ['group' => 'security', 'key' => 'login_max_attempts',     'value' => '5',                      'type' => 'integer', 'description' => 'Max failed login attempts before lockout', 'is_public' => false],
            ['group' => 'security', 'key' => 'login_lockout_minutes',  'value' => '15',                     'type' => 'integer', 'description' => 'Lockout duration in minutes',              'is_public' => false],
            ['group' => 'security', 'key' => 'session_lifetime',       'value' => '120',                    'type' => 'integer', 'description' => 'Session lifetime in minutes',              'is_public' => false],
            ['group' => 'security', 'key' => 'password_min_length',    'value' => '8',                      'type' => 'integer', 'description' => 'Minimum password length',                  'is_public' => true],
            ['group' => 'security', 'key' => 'require_email_verify',   'value' => 'true',                   'type' => 'boolean', 'description' => 'Require email verification on signup',     'is_public' => false],
            ['group' => 'security', 'key' => 'two_factor_optional',    'value' => 'true',                   'type' => 'boolean', 'description' => 'Allow optional 2FA (not forced)',          'is_public' => false],

            // ── Mail ──────────────────────────────────────────────────────────
            ['group' => 'mail',     'key' => 'from_name',              'value' => 'VJ CPA CRM',             'type' => 'string',  'description' => 'Default from name for emails',             'is_public' => false],
            ['group' => 'mail',     'key' => 'from_address',           'value' => 'no-reply@cpacrm.com',    'type' => 'string',  'description' => 'Default from address for emails',          'is_public' => false],
            ['group' => 'mail',     'key' => 'queue_emails',           'value' => 'true',                   'type' => 'boolean', 'description' => 'Queue all outbound emails',                'is_public' => false],

            // ── Storage ───────────────────────────────────────────────────────
            ['group' => 'storage',  'key' => 'driver',                 'value' => 'local',                  'type' => 'string',  'description' => 'File storage driver (local|s3)',            'is_public' => false],
            ['group' => 'storage',  'key' => 'max_upload_mb',          'value' => '50',                     'type' => 'integer', 'description' => 'Maximum file upload size in MB',           'is_public' => true],
            ['group' => 'storage',  'key' => 'allowed_extensions',     'value' => 'pdf,doc,docx,xls,xlsx,png,jpg,jpeg,csv,txt,zip', 'type' => 'string', 'description' => 'Allowed upload extensions', 'is_public' => true],

            // ── Accounting ────────────────────────────────────────────────────
            ['group' => 'accounting', 'key' => 'decimal_places',       'value' => '2',                      'type' => 'integer', 'description' => 'Default decimal places for money display', 'is_public' => true],
            ['group' => 'accounting', 'key' => 'rounding_mode',        'value' => 'HALF_UP',                'type' => 'string',  'description' => 'Money rounding mode',                     'is_public' => false],

            // ── Notifications ─────────────────────────────────────────────────
            ['group' => 'notifications', 'key' => 'email_enabled',     'value' => 'true',                   'type' => 'boolean', 'description' => 'Enable email notifications',               'is_public' => false],
            ['group' => 'notifications', 'key' => 'sms_enabled',       'value' => 'false',                  'type' => 'boolean', 'description' => 'Enable SMS notifications',                 'is_public' => false],
            ['group' => 'notifications', 'key' => 'in_app_enabled',    'value' => 'true',                   'type' => 'boolean', 'description' => 'Enable in-app notifications',              'is_public' => false],
            ['group' => 'notifications', 'key' => 'digest_enabled',    'value' => 'true',                   'type' => 'boolean', 'description' => 'Enable daily digest emails',               'is_public' => false],

            // ── AI ────────────────────────────────────────────────────────────
            ['group' => 'ai',       'key' => 'enabled',                'value' => 'false',                  'type' => 'boolean', 'description' => 'Enable AI assistant features',             'is_public' => false],
            ['group' => 'ai',       'key' => 'provider',               'value' => 'openai',                 'type' => 'string',  'description' => 'AI provider (openai|gemini)',               'is_public' => false],
        ];

        $now = now();
        foreach ($settings as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);

        // Upsert so re-running doesn't error
        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }
}
