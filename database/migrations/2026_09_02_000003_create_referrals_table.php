<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Referral system — 3NF design.
 *
 * referral_links     — unique link per referring tenant
 * referrals          — one row per referred signup
 * referral_rewards   — reward ledger (points/credit earned per referral)
 * referral_redemptions — how and when rewards were spent
 *
 * Points / credit balance is always computed from ledger, never stored
 * as a single mutable column (prevents race conditions and audit loss).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Referral link — one per tenant ───────────────────────────────────
        Schema::create('referral_links', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignId('tenant_id')
                ->unique() // One link per tenant
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->string('code', 40)->unique();     // URL-safe code: REF-XXXXX
            $table->unsignedInteger('click_count')->default(0);
            $table->unsignedInteger('signup_count')->default(0); // Denormalised
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code', 'is_active']);
        });

        // ── One row per referred signup ───────────────────────────────────────
        Schema::create('referrals', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            // The tenant who shared the link (referrer)
            $table->foreignId('referrer_tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignUlid('referral_link_id')
                ->constrained('referral_links')
                ->cascadeOnDelete();

            // The tenant who signed up via the link (referee)
            $table->foreignId('referee_tenant_id')
                ->nullable() // null until they complete signup
                ->constrained('tenants')
                ->nullOnDelete();

            // Tracking
            $table->string('referee_email')->nullable();    // Email before signup
            $table->string('referee_ip', 45)->nullable();
            $table->string('status', 20)->default('pending'); // ReferralStatus enum
            $table->timestamp('clicked_at')->useCurrent();
            $table->timestamp('signed_up_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamp('expires_at')->nullable();    // Auto-expire if no signup
            $table->string('revoke_reason')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['referrer_tenant_id', 'status']);
            $table->index(['referee_tenant_id', 'status']);
            $table->index(['referral_link_id', 'status']);
            $table->index(['status', 'expires_at']);
        });

        // ── Reward ledger — immutable, append-only ────────────────────────────
        Schema::create('referral_rewards', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('tenant_id')      // Referrer tenant receiving the reward
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignUlid('referral_id')
                ->constrained('referrals')
                ->cascadeOnDelete();

            $table->string('reward_type', 20);   // points|credit (RewardType enum)
            $table->decimal('amount', 20, 6);    // Points count or credit amount
            $table->foreignId('currency_id')     // Only relevant for credit type (FK)
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->string('description')->nullable(); // "Referral bonus for [firm name]"

            // Ledger entry type: earn or spend
            $table->string('entry_type', 10)->default('earn'); // earn|spend|expire|refund

            // Balance snapshot at the time of this entry (for audit convenience)
            $table->decimal('balance_after', 20, 6)->default(0);

            // Immutable
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'reward_type', 'created_at']);
            $table->index(['referral_id']);
        });

        // ── Reward redemption — when and how rewards were spent ───────────────
        Schema::create('referral_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->foreignUlid('subscription_id')
                ->nullable()
                ->constrained('subscriptions')
                ->nullOnDelete();
            $table->string('reward_type', 20);    // RewardType enum
            $table->decimal('amount_redeemed', 20, 6);
            $table->decimal('discount_applied', 20, 6); // Actual billing credit applied
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();
            $table->string('description')->nullable();
            $table->timestamp('redeemed_at')->useCurrent();

            $table->index(['tenant_id', 'redeemed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_redemptions');
        Schema::dropIfExists('referral_rewards');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('referral_links');
    }
};
