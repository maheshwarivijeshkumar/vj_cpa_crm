<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()
                ->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->string('user_name', 160)->nullable();   // Snapshot — user may be deleted
            $table->string('event_type', 60)->index();      // e.g. "created", "updated", "login"
            $table->string('module', 60)->nullable()->index();
            $table->string('resource_type', 100)->nullable()->index(); // Model class
            $table->string('resource_id', 100)->nullable();
            $table->string('resource_label')->nullable();   // Human-readable e.g. "John Smith"
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            // Immutable — no updated_at, no soft delete
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'created_at']);
            $table->index(['tenant_id', 'event_type']);
            $table->index(['tenant_id', 'resource_type', 'resource_id']);
            $table->index(['tenant_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
