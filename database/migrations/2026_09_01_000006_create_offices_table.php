<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Offices table — 3NF compliant.
 * Country and timezone are FK IDs, not stored strings.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            // ── Identity ──────────────────────────────────────────────────────
            $table->string('name');
            $table->string('code', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();

            // ── Address ───────────────────────────────────────────────────────
            $table->text('address_line1')->nullable();
            $table->text('address_line2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            // FK to countries (3NF — no raw string duplication)
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            // ── Locale ────────────────────────────────────────────────────────
            // FK to timezones (override tenant TZ if set)
            $table->foreignId('timezone_id')
                ->nullable()
                ->constrained('timezones')
                ->nullOnDelete();

            // ── Config ────────────────────────────────────────────────────────
            $table->json('settings')->nullable();
            $table->boolean('is_headquarters')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────────────
            $table->index(['tenant_id', 'is_active']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
