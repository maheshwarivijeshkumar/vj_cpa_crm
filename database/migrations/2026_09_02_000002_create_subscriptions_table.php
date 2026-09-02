<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscriptions — tracks tenant plan billing lifecycle.
 *
 * 3NF: no plan pricing stored here — price is denormalized from
 * TenantPlan enum at billing time and stored in amount_paid.
 * currency_id -> currencies (FK, not raw string).
 *
 * Also adds the deferred FK on discount_usages.subscription_id which
 * could not be set in migration 000001 (subscriptions didn't exist yet).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            // Tenant scope — never null
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            // Plan + status
            $table->string('plan', 40);    // trial|starter|professional|enterprise
            $table->string('status', 30);  // active|trial|past_due|lapsed|cancelled

            // Billing period
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('trial_ends_at')->nullable();

            // Financial
            $table->decimal('amount_paid', 20, 6)->default(0);
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // Discount applied at billing time
            // char(26) = ULID to match discounts.id
            $table->char('discount_id', 26)->nullable();
            $table->decimal('discount_amount', 20, 6)->default(0);

            // Billing metadata
            $table->string('billing_cycle', 20)->default('monthly'); // monthly|annual
            $table->string('payment_reference')->nullable();
            $table->string('payment_method', 40)->nullable();
            $table->json('metadata')->nullable();

            // Lifecycle timestamps
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('lapsed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'ends_at']);
            $table->index(['status', 'ends_at']); // For LapseExpiredSubscriptionsJob
        });

        // FK: subscriptions.discount_id -> discounts.id (ULID char 26)
        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->foreign('discount_id')
                ->references('id')
                ->on('discounts')
                ->nullOnDelete();
        });

        // Deferred FK from migration 000001:
        // discount_usages.subscription_id -> subscriptions.id
        // Only safe to add now that subscriptions exists.
        Schema::table('discount_usages', function (Blueprint $table): void {
            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Remove deferred FK before dropping subscriptions
        Schema::table('discount_usages', function (Blueprint $table): void {
            $table->dropForeign(['subscription_id']);
        });

        Schema::dropIfExists('subscriptions');
    }
};
