<?php

use App\Models\CameraPreset;
use App\Models\FilmRoll;
use App\Models\User;

test('mobile user can register via API', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Mobile User',
        'email' => 'mobile@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['success', 'message', 'data' => ['token', 'user']]);
});

test('mobile user can login and access protected routes', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $loginResponse = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $loginResponse->assertStatus(200)->assertJsonPath('success', true);
    $token = $loginResponse->json('data.token');

    $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/v1/auth/me');

    $meResponse->assertStatus(200)
        ->assertJsonPath('data.user.email', $user->email);
});

test('login with bad credentials returns error envelope', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Validation failed')
        ->assertJsonStructure(['errors' => ['email']]);
});

test('mobile user can fetch presets and create a film roll', function () {
    $preset = CameraPreset::create([
        'preset_key' => 'test_summer_35',
        'name' => 'Test Summer 35',
        'version' => 1,
        'engine' => 'film_v1',
        'lut' => 'cameras/summer-35.cube',
        'lut_checksum' => '696217c8d564711b8a60b8e6857dbba2cad4e2db2a4f43271696593031477be5',
        'grain' => 0.35,
        'bloom' => 0.20,
        'vignette' => 0.15,
        'aspect_ratio' => '4:3',
        'is_premium' => true,
    ]);

    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $presetsResponse = $this->getJson('/api/v1/camera-presets');
    $presetsResponse->assertStatus(200)
        ->assertJsonFragment([
            'id' => 'test_summer_35',
            'version' => 1,
            'engine' => 'film_v1',
            'lut_checksum' => '696217c8d564711b8a60b8e6857dbba2cad4e2db2a4f43271696593031477be5',
            'aspect_ratio' => '4:3',
            'premium' => true,
        ]);

    $rollResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/rolls', [
            'title' => 'My First Vintage Roll',
            'camera_preset_id' => $preset->id,
            'max_photos' => 36,
        ]);

    $rollResponse->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'My First Vintage Roll')
        ->assertJsonPath('data.user_role', 'owner')
        ->assertJsonPath('data.member_count', 1);
});

test('mobile user can join a film roll with invite code', function () {
    $owner = User::factory()->create();
    $roll = FilmRoll::factory()->create([
        'user_id' => $owner->id,
        'title' => 'Shared Party Roll',
        'invite_code' => 'PARTY2026',
    ]);

    $friend = User::factory()->create();
    $token = $friend->createToken('test-token')->plainTextToken;

    $joinResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/rolls/join', [
            'invite_code' => 'PARTY2026',
        ]);

    $joinResponse->assertStatus(200)
        ->assertJsonPath('data.name', 'Shared Party Roll')
        ->assertJsonPath('data.user_role', 'contributor');
});
