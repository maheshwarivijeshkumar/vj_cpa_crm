<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timezones', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('zone_name', 100);       // e.g. America/New_York
            $table->integer('gmt_offset');           // seconds offset from UTC
            $table->string('gmt_offset_name', 20);  // e.g. UTC-05:00
            $table->string('abbreviation', 20)->nullable(); // e.g. EST
            $table->string('tz_name', 150)->nullable();     // e.g. Eastern Standard Time
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['country_id', 'zone_name']);
            $table->index('zone_name');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timezones');
    }
};
