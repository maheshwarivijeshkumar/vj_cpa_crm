<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscriptions â€” tracks tenant plan billing lifecycle.
 *
 * 3NF: no plan pricing stored here â€” price is denormalized from
 * TenantPlan enum at billing time and stored in amount_paid.
 * currency_id â†’ currencies (FK, not char column).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            // Tenant scope â€” never null (subscriptions always belong to a tenant)
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            // Plan reference
            $table->string('plan', 40);   // trial|starter|professional|enterprise
            $table->string('status', 30); // active|trial|past_due|lapsed|cancelled

            // Billing period
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('trial_ends_at')->nullable();

            // Financial (3NF: currency FK, not raw string)
            $table->decimal('amount_paid', 20, 6)->default(0);
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // Discount applied at billing time
            $table->char('discount_id', 26)->nullable();
            $table->decimal('discount_amount', 20, 6)->default(0);

            // Billing metadata
            $table->string('billing_cycle', 20)->default('monthly'); // monthly|annual
            $table->string('payment_reference')->nullable();          // External payment ID
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
            $table->index(['status', 'ends_at']); // For scheduled lapse jobs
        });

        // Add FK after table exists — discount_id references discounts.id (char 26 ULID)
        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->foreign('discount_id')
                ->references('id')
                ->on('discounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
