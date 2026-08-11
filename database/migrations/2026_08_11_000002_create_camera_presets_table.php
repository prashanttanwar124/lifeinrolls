<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camera_presets', function (Blueprint $table) {
            $table->id();
            $table->string('preset_key')->unique(); // e.g. "summer_35"
            $table->string('name');
            $table->string('engine')->default('film_v1');
            $table->integer('version')->default(1);
            $table->string('lut')->default('cameras/summer-35.cube');
            $table->string('lut_checksum')->nullable();
            $table->decimal('lut_intensity', 3, 2)->default(0.85);
            $table->decimal('grain', 3, 2)->default(0.30);
            $table->decimal('bloom', 3, 2)->default(0.20);
            $table->decimal('vignette', 3, 2)->default(0.15);
            $table->decimal('softness', 3, 2)->default(0.10);
            $table->boolean('date_stamp')->default(false);
            $table->string('aspect_ratio')->default('4:3');
            $table->boolean('is_premium')->default(false);
            $table->string('minimum_app_version')->default('0.1.0');
            $table->text('description')->nullable();
            $table->string('sample_image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_presets');
    }
};
