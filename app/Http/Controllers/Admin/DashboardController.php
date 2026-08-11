<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CameraPreset;
use App\Models\FilmRoll;
use App\Models\Photo;
use App\Models\PhotoReport;
use App\Models\SupportRequest;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_users' => User::count(),
            'total_rolls' => FilmRoll::count(),
            'active_rolls' => FilmRoll::where('status', 'active')->count(),
            'total_photos' => Photo::count(),
            'pending_reports' => PhotoReport::where('status', 'pending')->count(),
            'open_support' => SupportRequest::where('status', 'open')->count(),
            'total_presets' => CameraPreset::count(),
        ];

        $recentRolls = FilmRoll::with(['creator', 'cameraPreset'])->latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'recentRolls' => $recentRolls,
            'recentUsers' => $recentUsers,
        ]);
    }
}
