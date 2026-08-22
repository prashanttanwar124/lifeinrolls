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

        $storageInfo = [
            'disk' => config('filesystems.default'),
            'bucket' => config('filesystems.disks.s3.bucket'),
            'endpoint' => config('filesystems.disks.s3.endpoint'),
            'url' => config('filesystems.disks.s3.url'),
            'has_key' => ! empty(config('filesystems.disks.s3.key')),
            'has_secret' => ! empty(config('filesystems.disks.s3.secret')),
        ];

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'recentRolls' => $recentRolls,
            'recentUsers' => $recentUsers,
            'storageInfo' => $storageInfo,
        ]);
    }

    public function testStorage(): \Illuminate\Http\RedirectResponse
    {
        $disk = config('filesystems.default', 's3');
        $testFileName = 'diagnostic-test-' . time() . '.txt';
        $testContent = 'LifeInRolls R2 Storage Diagnostic Test at ' . now()->toIso8601String();

        try {
            // 1. Test Write
            \Illuminate\Support\Facades\Storage::disk($disk)->put($testFileName, $testContent);

            // 2. Test Exists
            $exists = \Illuminate\Support\Facades\Storage::disk($disk)->exists($testFileName);
            if (! $exists) {
                throw new \Exception("File was written but could not be confirmed as existing on disk [{$disk}].");
            }

            // 3. Test URL generation
            $url = \Illuminate\Support\Facades\Storage::disk($disk)->url($testFileName);

            // 4. Test Cleanup
            \Illuminate\Support\Facades\Storage::disk($disk)->delete($testFileName);

            return back()->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => "Cloudflare R2 / S3 test PASSED! Disk: [{$disk}], Bucket: [" . config('filesystems.disks.s3.bucket') . "]. File write, read, and delete succeeded. Test URL generated: {$url}",
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::error('Storage test failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('flash', [
                'toast' => [
                    'type' => 'danger',
                    'message' => "Storage test FAILED on [{$disk}]: " . $e->getMessage(),
                ],
            ])->withErrors([
                'storage_error' => $e->getMessage(),
            ]);
        }
    }
}
