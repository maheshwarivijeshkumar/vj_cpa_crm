<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notification Logs — immutable delivery log for every sent notification.
 *
 * Stores every in-app, email, and SMS notification sent to a user.
 * In-app rows are read-by-user (is_read flag). Email/SMS are for delivery audit.
 * Rows are cleaned up after 6 months by CleanupExpiredSessionsJob.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();

            // Tenant context
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('tenants')
                ->nullOnDelete();

            // Recipient
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('user_name', 160)->nullable();   // Snapshot — user may be deleted

            // Template reference (nullable — allows ad-hoc notifications)
            $table->foreignId('notification_template_id')
                ->nullable()
                ->constrained('notification_templates')
                ->nullOnDelete();

            $table->string('template_key', 120)->nullable(); // Denormalised for queries
            $table->string('channel', 20)->default('in_app');// email | in_app | sms

            // Content snapshot — what was actually delivered
            $table->string('subject')->nullable();
            $table->text('body')->nullable();

            // Delivery metadata
            $table->string('status', 30)->default('sent');   // sent | failed | pending
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();             // Extra context (filing_id, invoice_id, etc.)

            // In-app read state
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // Immutable — no updated_at (append-only log)
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['tenant_id', 'created_at']);
            $table->index(['user_id', 'channel', 'created_at']);
            $table->index('template_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
