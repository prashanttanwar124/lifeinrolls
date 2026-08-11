<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('film_roll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('camera_preset_id')->nullable()->constrained('camera_presets')->nullOnDelete();
            $table->string('photo_url');
            $table->string('thumbnail_url')->nullable();
            $table->string('caption')->nullable();
            $table->string('status')->default('approved'); // approved, pending_approval, rejected
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
