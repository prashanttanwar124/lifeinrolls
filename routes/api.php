<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CameraPresetController;
use App\Http\Controllers\Api\V1\FilmRollController;
use App\Http\Controllers\Api\V1\FilmRollMemberController;
use App\Http\Controllers\Api\V1\PhotoController;
use App\Http\Controllers\Api\V1\SubscriptionPlanController;
use App\Http\Controllers\Api\V1\SupportRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile App API Routes (V1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Public Presets & Subscription Plans
    Route::get('/camera-presets', [CameraPresetController::class, 'index']);
    Route::get('/camera-presets/{preset}', [CameraPresetController::class, 'show']);
    Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index']);

    // Protected Routes (Sanctum Auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Film Rolls
        Route::get('/rolls', [FilmRollController::class, 'index']);
        Route::post('/rolls', [FilmRollController::class, 'store']);
        Route::post('/rolls/join', [FilmRollController::class, 'join']);
        Route::get('/rolls/{roll}', [FilmRollController::class, 'show']);
        Route::match(['put', 'patch'], '/rolls/{roll}', [FilmRollController::class, 'update']);
        Route::delete('/rolls/{roll}', [FilmRollController::class, 'destroy']);

        // Members & Invitations
        Route::get('/rolls/{roll}/members', [FilmRollMemberController::class, 'index']);
        Route::patch('/rolls/{roll}/members/{member}', [FilmRollMemberController::class, 'update']);
        Route::delete('/rolls/{roll}/members/{member}', [FilmRollMemberController::class, 'destroy']);
        Route::get('/rolls/{roll}/invite', [FilmRollMemberController::class, 'invite']);
        Route::post('/rolls/{roll}/invite/regenerate', [FilmRollMemberController::class, 'regenerateInvite']);

        // Shared Photos & Gallery
        Route::get('/rolls/{roll}/photos', [PhotoController::class, 'index']);
        Route::post('/rolls/{roll}/photos', [PhotoController::class, 'store']);
        Route::patch('/photos/{photo}/status', [PhotoController::class, 'updateStatus']);
        Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);
        Route::post('/photos/{photo}/approve', [PhotoController::class, 'approve']);
        Route::post('/photos/{photo}/reject', [PhotoController::class, 'reject']);
        Route::post('/photos/{photo}/report', [PhotoController::class, 'report']);

        // Support Requests
        Route::get('/support-requests', [SupportRequestController::class, 'index']);
        Route::post('/support-requests', [SupportRequestController::class, 'store']);
    });
});
