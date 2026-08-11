<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('film_rolls', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('description');
            $table->string('invite_token', 64)->nullable()->unique()->after('invite_code');
            $table->timestamp('starts_at')->nullable()->after('status');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
            $table->timestamp('reveal_at')->nullable()->after('ends_at'); // surprise mode reveal time
            $table->timestamp('archived_at')->nullable()->after('reveal_at');
        });

        Schema::table('photos', function (Blueprint $table) {
            // uploaded, processing, ready, failed — separate from the approval status column
            $table->string('upload_status')->default('ready')->after('status');
        });

        // Backfill invite tokens for existing rolls
        DB::table('film_rolls')->whereNull('invite_token')->orderBy('id')->each(function ($roll) {
            DB::table('film_rolls')->where('id', $roll->id)->update([
                'invite_token' => Str::random(40),
            ]);
        });

        // Migrate legacy 'member' role to 'contributor' (roles: owner, admin, contributor, viewer)
        DB::table('film_roll_members')->where('role', 'member')->update(['role' => 'contributor']);

        // Ensure every roll creator has an owner membership row
        DB::table('film_rolls')->orderBy('id')->each(function ($roll) {
            $exists = DB::table('film_roll_members')
                ->where('film_roll_id', $roll->id)
                ->where('user_id', $roll->user_id)
                ->exists();

            if (! $exists) {
                DB::table('film_roll_members')->insert([
                    'film_roll_id' => $roll->id,
                    'user_id' => $roll->user_id,
                    'role' => 'owner',
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('film_rolls', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'invite_token', 'starts_at', 'ends_at', 'reveal_at', 'archived_at']);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('upload_status');
        });

        DB::table('film_roll_members')->where('role', 'contributor')->update(['role' => 'member']);
    }
};
