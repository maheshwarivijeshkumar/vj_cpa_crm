<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the default Laravel users table with CPA CRM fields.
 *
 * 3NF compliance:
 *  - timezone_id  → FK to timezones
 *  - language_id  → FK to languages
 *  - currency_id  → FK to currencies
 *  No raw strings for reference data that already has a table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Drop the generic single-name column (replaced by first/last)
            $table->dropColumn('name');

            // ── Identity ──────────────────────────────────────────────────────
            $table->uuid('uuid')->unique()->after('id');

            $table->foreignId('tenant_id')
                ->nullable()->after('uuid')
                ->constrained('tenants')->nullOnDelete();

            $table->foreignId('office_id')
                ->nullable()->after('tenant_id')
                ->constrained('offices')->nullOnDelete();

            $table->string('username', 60)->unique()->nullable()->after('office_id');
            $table->string('first_name', 80)->after('username');
            $table->string('last_name', 80)->after('first_name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('avatar_path')->nullable()->after('phone');

            // ── Access / Role ──────────────────────────────────────────────────
            // platform_admin | firm_owner | firm_user | client
            $table->string('user_type', 30)->default('firm_user')->after('avatar_path');

            // active | inactive | suspended | invited | archived
            $table->string('status', 30)->default('active')->after('user_type');

            // ── Security ──────────────────────────────────────────────────────
            $table->boolean('must_change_password')->default(false)->after('status');
            $table->boolean('two_factor_enabled')->default(false)->after('must_change_password');
            $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            $table->timestamp('last_login_at')->nullable()->after('two_factor_confirmed_at');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');

            // ── Locale Preferences (FK — 3NF) ─────────────────────────────────
            $table->foreignId('timezone_id')
                ->nullable()->after('last_login_ip')
                ->constrained('timezones')->nullOnDelete();

            $table->foreignId('language_id')
                ->nullable()->after('timezone_id')
                ->constrained('languages')->nullOnDelete();

            $table->foreignId('currency_id')
                ->nullable()->after('language_id')
                ->constrained('currencies')->nullOnDelete();

            // User's preferred date/number display format (UI only — no FK needed)
            $table->string('date_format', 30)->nullable()->after('currency_id');
            $table->string('number_format', 10)->nullable()->after('date_format');

            // Arbitrary UI prefs (theme, layout, table density, etc.)
            $table->json('preferences')->nullable()->after('number_format');

            // ── Lifecycle ──────────────────────────────────────────────────────
            $table->timestamp('invited_at')->nullable()->after('preferences');
            $table->timestamp('archived_at')->nullable()->after('invited_at');
            $table->softDeletes()->after('updated_at');

            // ── Indexes ───────────────────────────────────────────────────────
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'user_type']);
            $table->index('user_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('name')->after('id');
            $table->dropColumn([
                'uuid', 'tenant_id', 'office_id', 'username',
                'first_name', 'last_name', 'phone', 'avatar_path',
                'user_type', 'status',
                'must_change_password', 'two_factor_enabled',
                'two_factor_secret', 'two_factor_recovery_codes',
                'two_factor_confirmed_at', 'last_login_at', 'last_login_ip',
                'timezone_id', 'language_id', 'currency_id',
                'date_format', 'number_format', 'preferences',
                'invited_at', 'archived_at', 'deleted_at',
            ]);
        });
    }
};
