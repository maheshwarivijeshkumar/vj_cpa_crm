<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Discounts — platform-managed discount codes.
 *
 * 3NF design:
 *  - Applicability scope split into: discount_tenant_assignments pivot (for specific tenants)
 *  - Currency stored as FK (currency_id -> currencies), not raw char
 *  - No duplicate data: discount_usages table tracks redemption history separately
 *
 * NOTE: discount_usages.subscription_id FK is deferred to migration 000002
 *       because the subscriptions table does not exist yet at this point.
 *
 * Supports: fixed/percentage, welcome/winback/referral/manual triggers,
 * all-tenant or specific-tenant scope, usage limits, expiry dates.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            // Who created it (platform admin or system auto-generated)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Identity
            $table->string('code', 60)->unique();       // e.g. WELCOME20, WINBACK15
            $table->string('name', 150);                 // Human label
            $table->text('description')->nullable();     // Internal notes

            // Value
            $table->string('type', 20);                  // fixed|percentage (DiscountType enum)
            $table->decimal('value', 20, 6);              // Amount or percentage (0-100)
            $table->decimal('max_discount_amount', 20, 6)->nullable(); // Cap for percentage

            // Currency (FK — 3NF)
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // Scope
            $table->string('applicability', 20)->default('all'); // all|specific|plan
            $table->json('applicable_plans')->nullable();          // Array of plan keys
            $table->json('applicable_tenant_ids')->nullable();     // Array of tenant IDs

            // Validity window
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();

            // Usage limits
            $table->unsignedInteger('max_uses')->nullable();        // null = unlimited
            $table->unsignedInteger('max_uses_per_tenant')->default(1);
            $table->unsignedInteger('uses_count')->default(0);      // Denormalised counter

            // Trigger + Status
            $table->string('trigger', 30)->default('manual');       // DiscountTrigger enum
            $table->string('status', 20)->default('active');        // active|inactive|expired|depleted

            // Whether the platform auto-emails this code when generated
            $table->boolean('auto_email')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'valid_until']);
            $table->index(['trigger', 'status']);
            $table->index('code');
        });

        // Pivot: specific tenant assignments (when applicability = 'specific')
        Schema::create('discount_tenant_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignUlid('discount_id')
                ->constrained('discounts')
                ->cascadeOnDelete();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['discount_id', 'tenant_id']);
            $table->index('tenant_id');
        });

        // Redemption history — immutable ledger (no updates, no deletes).
        // IMPORTANT: subscription_id FK is added in migration 000002 after
        //            the subscriptions table is created. Only the column is defined here.
        Schema::create('discount_usages', function (Blueprint $table): void {
            $table->id();
            $table->foreignUlid('discount_id')
                ->constrained('discounts')
                ->cascadeOnDelete();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            // char(26) = ULID — matches subscriptions.id; FK deferred to migration 000002
            $table->char('subscription_id', 26)->nullable();
            $table->decimal('original_amount', 20, 6); // Before discount
            $table->decimal('discount_amount', 20, 6); // Amount saved
            $table->decimal('final_amount', 20, 6);    // After discount
            $table->timestamp('used_at')->useCurrent();

            $table->index(['discount_id', 'tenant_id']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_usages');
        Schema::dropIfExists('discount_tenant_assignments');
        Schema::dropIfExists('discounts');
    }
};
