<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CameraPresetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PhotoReportController;
use App\Http\Controllers\Admin\RollController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Admin Management Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Film Rolls
        Route::get('/rolls', [RollController::class, 'index'])->name('rolls.index');
        Route::delete('/rolls/{roll}', [RollController::class, 'destroy'])->name('rolls.destroy');

        // Camera Presets
        Route::get('/presets', [CameraPresetController::class, 'index'])->name('presets.index');
        Route::post('/presets', [CameraPresetController::class, 'store'])->name('presets.store');
        Route::post('/presets/{preset}/toggle', [CameraPresetController::class, 'toggle'])->name('presets.toggle');
        Route::delete('/presets/{preset}', [CameraPresetController::class, 'destroy'])->name('presets.destroy');

        // Photo Reports Review
        Route::get('/reports', [PhotoReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/{report}/dismiss', [PhotoReportController::class, 'dismiss'])->name('reports.dismiss');
        Route::delete('/reports/{report}/photo', [PhotoReportController::class, 'deletePhoto'])->name('reports.delete-photo');

        // Subscriptions
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');

        // Support Tickets
        Route::get('/support', [SupportController::class, 'index'])->name('support.index');
        Route::post('/support/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply');

        // Banners & Notifications
        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    });
});

// Shareable invite link landing page — shows the join code so the user can enter it in the app.
Route::get('/join/{token}', function (string $token) {
    $roll = \App\Models\FilmRoll::where('invite_token', $token)->whereNull('archived_at')->firstOrFail();

    return response()->view('roll-invite', [
        'rollTitle' => $roll->title,
        'inviteCode' => $roll->invite_code,
    ]);
})->name('rolls.invite-link');

require __DIR__.'/settings.php';
