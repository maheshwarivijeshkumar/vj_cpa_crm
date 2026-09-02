<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tenants table — 3NF compliant.
 *
 * Country, currency, timezone, language are stored as FK IDs
 * pointing to their respective reference tables.
 * No raw string duplication of reference data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();

            // ── Identity ──────────────────────────────────────────────────────
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('website')->nullable();

            // ── Address ───────────────────────────────────────────────────────
            $table->text('address_line1')->nullable();
            $table->text('address_line2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            // FK to countries table (3NF)
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            // ── Locale / Regional (all FKs to reference tables) ───────────────
            // FK to timezones table
            $table->foreignId('timezone_id')
                ->nullable()
                ->constrained('timezones')
                ->nullOnDelete();

            // FK to languages table
            $table->foreignId('language_id')
                ->nullable()
                ->constrained('languages')
                ->nullOnDelete();

            // FK to currencies table
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // ── Fiscal / Business ──────────────────────────────────────────────
            $table->tinyInteger('fiscal_year_start_month')->default(1);  // 1–12
            $table->tinyInteger('fiscal_year_start_day')->default(1);    // 1–31

            // ── Branding ──────────────────────────────────────────────────────
            $table->string('logo_path')->nullable();
            $table->json('brand_colors')->nullable(); // {"primary":"#1D9792"}

            // ── Subscription / Plan ───────────────────────────────────────────
            $table->string('plan', 40)->default('trial');
            // trial | starter | professional | enterprise

            $table->string('status', 30)->default('trial');
            // trial | active | suspended | cancelled

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('suspended_at')->nullable();

            // ── Settings / Metadata ───────────────────────────────────────────
            $table->json('settings')->nullable();   // Tenant-level overrides
            $table->json('metadata')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────────────
            $table->index('status');
            $table->index('plan');
            $table->index('is_active');
            $table->index('country_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
