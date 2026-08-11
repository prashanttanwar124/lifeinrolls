<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('film_rolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('invite_code')->unique();
            $table->integer('max_photos')->default(36);
            $table->integer('current_photos')->default(0);
            $table->string('roll_type')->default('standard'); // standard, live, surprise, approval
            $table->string('status')->default('active'); // active, locked, completed
            $table->foreignId('camera_preset_id')->nullable()->constrained('camera_presets')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('film_rolls');
    }
};
