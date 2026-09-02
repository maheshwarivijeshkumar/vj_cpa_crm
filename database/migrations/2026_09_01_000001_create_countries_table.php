<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 100);
            $table->char('iso2', 2)->nullable()->unique();
            $table->char('iso3', 3)->nullable()->unique();
            $table->char('numeric_code', 3)->nullable()->unique();
            $table->string('phonecode', 20)->nullable();
            $table->string('capital')->nullable();
            $table->string('tld', 20)->nullable();
            $table->string('native')->nullable();
            $table->string('nationality')->nullable();
            $table->string('emoji', 20)->nullable();
            $table->string('emoji_u', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
