<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notification Templates — stores all branded email/in-app/SMS message templates.
 *
 * Resolution hierarchy: Office → Tenant → Platform → System fallback
 * (handled by TemplateResolverService / SettingsService).
 *
 * Variables use {{double_curly}} syntax: {{user.first_name}}, {{firm.name}}
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table): void {
            $table->id();

            // Scope — null tenant_id = platform/system template
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('tenants')
                ->cascadeOnDelete();

            // Template identification
            $table->string('key', 120);          // e.g. 'auth.welcome', 'filing.deadline.7d'
            $table->string('name', 150);          // Human label: "Welcome Email"
            $table->string('channel', 20)         // 'email' | 'in_app' | 'sms'
                  ->default('email');
            $table->string('category', 60)        // 'auth' | 'filing' | 'billing' | 'system'
                  ->default('system');

            // Content — channel-specific fields
            $table->string('subject')->nullable();          // Email subject
            $table->text('body_html')->nullable();           // Email HTML body (with {{vars}})
            $table->text('body_text')->nullable();           // Plain-text fallback
            $table->text('body_short')->nullable();          // SMS / in-app short message

            // Template metadata
            $table->json('available_variables')->nullable(); // ["user.first_name","firm.name"]
            $table->text('description')->nullable();         // Internal notes

            // Version control
            $table->string('status', 20)->default('published'); // draft | review | published | archived
            $table->unsignedSmallInteger('version')->default(1);

            // Flags
            $table->boolean('is_system')->default(false);    // Cannot be deleted by tenants
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'key', 'channel'], 'uq_notification_templates_tenant_key_channel');
            $table->index(['key', 'channel', 'is_active']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
