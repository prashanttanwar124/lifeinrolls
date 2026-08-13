<?php

namespace Database\Seeders;

use App\Models\CameraPreset;
use App\Models\FilmRoll;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Test Customer User
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // Mobile Camera Profiles & Presets
        $presets = [
            [
                'preset_key' => 'summer_35',
                'name' => 'Summer 35',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/summer-35.cube',
                'lut_checksum' => '696217c8d564711b8a60b8e6857dbba2cad4e2db2a4f43271696593031477be5',
                'lut_intensity' => 0.85,
                'grain' => 0.30,
                'bloom' => 0.20,
                'vignette' => 0.15,
                'softness' => 0.10,
                'date_stamp' => false,
                'aspect_ratio' => '4:3',
                'is_premium' => false,
                'minimum_app_version' => '0.1.0',
                'description' => 'Bright summer tones with warm highlights and soft grain.',
                'is_active' => true,
            ],
            [
                'preset_key' => 'kodak_portra_400',
                'name' => 'Kodak Portra 400',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/portra-400.cube',
                'lut_intensity' => 0.90,
                'grain' => 0.45,
                'bloom' => 0.15,
                'vignette' => 0.20,
                'softness' => 0.05,
                'date_stamp' => false,
                'aspect_ratio' => '4:3',
                'is_premium' => false,
                'minimum_app_version' => '0.1.0',
                'description' => 'Warm, natural skin tones with fine film grain.',
                'is_active' => true,
            ],
            [
                'preset_key' => 'fuji_superia_400',
                'name' => 'Fuji Superia 400',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/superia-400.cube',
                'lut_intensity' => 0.80,
                'grain' => 0.50,
                'bloom' => 0.10,
                'vignette' => 0.25,
                'softness' => 0.05,
                'date_stamp' => false,
                'aspect_ratio' => '3:2',
                'is_premium' => false,
                'minimum_app_version' => '0.1.0',
                'description' => 'Vibrant greens and cool shadows with classic analog feel.',
                'is_active' => true,
            ],
            [
                'preset_key' => 'cinestill_800t',
                'name' => 'CineStill 800T',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/cinestill-800t.cube',
                'lut_intensity' => 0.95,
                'grain' => 0.60,
                'bloom' => 0.40,
                'vignette' => 0.30,
                'softness' => 0.15,
                'date_stamp' => false,
                'aspect_ratio' => '16:9',
                'is_premium' => true,
                'minimum_app_version' => '0.1.0',
                'description' => 'Tungsten-balanced cinema film effect with red halation glow.',
                'is_active' => true,
            ],
            [
                'preset_key' => 'noir_800',
                'name' => 'Noir 800',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/noir-800.cube',
                'lut_intensity' => 1.00,
                'grain' => 0.55,
                'bloom' => 0.05,
                'vignette' => 0.45,
                'softness' => 0.00,
                'date_stamp' => false,
                'aspect_ratio' => '4:3',
                'is_premium' => false,
                'minimum_app_version' => '0.1.0',
                'description' => 'High-contrast monochrome film noir with deep obsidian shadows.',
                'is_active' => true,
            ],
            [
                'preset_key' => 'dark_moody_35',
                'name' => 'Dark Moody 35',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/dark-moody-35.cube',
                'lut_intensity' => 0.90,
                'grain' => 0.40,
                'bloom' => 0.15,
                'vignette' => 0.38,
                'softness' => 0.05,
                'date_stamp' => false,
                'aspect_ratio' => '3:2',
                'is_premium' => false,
                'minimum_app_version' => '0.1.0',
                'description' => 'Deep emerald tones, cold shadows, and subdued analog highlights.',
                'is_active' => true,
            ],
            [
                'preset_key' => 'midnight_tokyo',
                'name' => 'Midnight Tokyo',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/midnight-tokyo.cube',
                'lut_intensity' => 0.92,
                'grain' => 0.50,
                'bloom' => 0.30,
                'vignette' => 0.35,
                'softness' => 0.10,
                'date_stamp' => false,
                'aspect_ratio' => '16:9',
                'is_premium' => true,
                'minimum_app_version' => '0.1.0',
                'description' => 'Moody cyber noir with deep midnight cyan shadows and neon glow.',
                'is_active' => true,
            ],
            [
                'preset_key' => 'shadow_chrome',
                'name' => 'Shadow Chrome',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/shadow-chrome.cube',
                'lut_intensity' => 0.88,
                'grain' => 0.35,
                'bloom' => 0.10,
                'vignette' => 0.30,
                'softness' => 0.05,
                'date_stamp' => false,
                'aspect_ratio' => '1:1',
                'is_premium' => false,
                'minimum_app_version' => '0.1.0',
                'description' => 'Matte crushed shadows with muted cinematic chrome finish.',
                'is_active' => true,
            ],
            [
                'preset_key' => 'cine_noir_500',
                'name' => 'Cine Noir 500',
                'engine' => 'film_v1',
                'version' => 1,
                'lut' => 'cameras/cine-noir-500.cube',
                'lut_intensity' => 0.95,
                'grain' => 0.60,
                'bloom' => 0.25,
                'vignette' => 0.50,
                'softness' => 0.10,
                'date_stamp' => false,
                'aspect_ratio' => '16:9',
                'is_premium' => true,
                'minimum_app_version' => '0.1.0',
                'description' => 'Heavy vignette cinematic tungsten noir with deep blacks.',
                'is_active' => true,
            ],
        ];

        foreach ($presets as $p) {
            CameraPreset::updateOrCreate(['preset_key' => $p['preset_key']], $p);
        }

        // Subscription Plans
        $plans = [
            [
                'name' => 'Free Starter',
                'slug' => 'free-starter',
                'price' => 0.00,
                'currency' => 'USD',
                'interval' => 'monthly',
                'max_rolls' => 3,
                'max_photos_per_roll' => 24,
                'allows_custom_presets' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Pro Photographer',
                'slug' => 'pro-photographer',
                'price' => 4.99,
                'currency' => 'USD',
                'interval' => 'monthly',
                'max_rolls' => 15,
                'max_photos_per_roll' => 36,
                'allows_custom_presets' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Unlimited Studio',
                'slug' => 'unlimited-studio',
                'price' => 9.99,
                'currency' => 'USD',
                'interval' => 'monthly',
                'max_rolls' => 999,
                'max_photos_per_roll' => 36,
                'allows_custom_presets' => true,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $pl) {
            SubscriptionPlan::firstOrCreate(['slug' => $pl['slug']], $pl);
        }

        // Sample Film Roll
        $preset = CameraPreset::first();
        FilmRoll::firstOrCreate(
            ['invite_code' => 'TOKYO2026'],
            [
                'user_id' => $user->id,
                'title' => 'Tokyo Night Walk',
                'description' => 'Street photography around Shibuya and Shinjuku',
                'invite_code' => 'TOKYO2026',
                'max_photos' => 36,
                'current_photos' => 12,
                'roll_type' => 'standard',
                'status' => 'active',
                'camera_preset_id' => $preset?->id,
            ]
        );
    }
}
