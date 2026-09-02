<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_flags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->nullable()      // null = platform-level flag
                ->constrained('tenants')->cascadeOnDelete();
            $table->string('key', 100);                     // e.g. "ai_assistant_enabled"
            $table->boolean('value')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'key']);
            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
    }
};
