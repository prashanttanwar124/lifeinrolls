<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('currency')->default('USD');
            $table->string('interval')->default('monthly'); // monthly, yearly
            $table->integer('max_rolls')->default(5);
            $table->integer('max_photos_per_roll')->default(36);
            $table->boolean('allows_custom_presets')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
