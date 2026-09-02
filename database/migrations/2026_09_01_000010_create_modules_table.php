<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 60)->unique();           // e.g. "clients", "taxation"
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_core')->default(false);     // Core = cannot be disabled
            $table->boolean('is_enabled')->default(true);   // Platform-level toggle
            $table->json('dependencies')->nullable();        // ["clients","accounting"]
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable();            // Module-level default config
            $table->timestamps();
        });

        // Per-tenant module activation
        Schema::create('tenant_modules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->boolean('is_enabled')->default(true);
            $table->json('settings')->nullable();            // Tenant module config
            $table->timestamps();

            $table->unique(['tenant_id', 'module_id']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_modules');
        Schema::dropIfExists('modules');
    }
};
