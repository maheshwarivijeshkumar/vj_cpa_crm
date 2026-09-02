<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Platform-level settings (single global row per key)
        Schema::create('system_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('group', 60)->index();           // e.g. "platform", "mail", "security"
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');  // string|boolean|integer|json
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);   // Expose to frontend
            $table->timestamps();

            $table->unique(['group', 'key']);
        });

        // Tenant-level settings
        Schema::create('tenant_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('group', 60)->index();
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->timestamps();

            $table->unique(['tenant_id', 'group', 'key']);
            $table->index('tenant_id');
        });

        // Office-level settings
        Schema::create('office_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->string('group', 60)->index();
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->timestamps();

            $table->unique(['office_id', 'group', 'key']);
            $table->index('office_id');
        });

        // User preferences
        Schema::create('user_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->timestamps();

            $table->unique(['user_id', 'key']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('office_settings');
        Schema::dropIfExists('tenant_settings');
        Schema::dropIfExists('system_settings');
    }
};
