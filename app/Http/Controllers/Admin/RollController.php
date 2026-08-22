<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilmRoll;
use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class RollController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        $rolls = FilmRoll::with(['creator', 'cameraPreset'])
            ->withCount(['memberships', 'photos'])
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('invite_code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/Rolls/Index', [
            'rolls' => $rolls,
            'filters' => ['search' => $search],
        ]);
    }

    public function show(FilmRoll $roll): Response
    {
        $roll->load(['creator', 'cameraPreset'])->loadCount(['memberships', 'photos']);

        return Inertia::render('admin/Rolls/Show', [
            'roll' => $roll,
            'members' => $roll->memberships()->with('user')->orderBy('joined_at')->get(),
            'photos' => $roll->photos()->with('user')->latest()->get(),
        ]);
    }

    public function destroy(FilmRoll $roll): RedirectResponse
    {
        $roll->delete();

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Film roll deleted successfully.',
            ],
        ]);
    }

    public function storePhoto(Request $request, FilmRoll $roll): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|max:20480', // 20MB
            'caption' => 'nullable|string|max:255',
        ]);

        try {
            $disk = config('filesystems.default', 's3');
            $path = $request->file('photo')->store("rolls/{$roll->id}", $disk);
            $photoUrl = Storage::disk($disk)->url($path);

            $photo = Photo::create([
                'film_roll_id' => $roll->id,
                'user_id' => $request->user()->id,
                'camera_preset_id' => $roll->camera_preset_id,
                'photo_url' => $photoUrl,
                'thumbnail_url' => $photoUrl,
                'caption' => $request->caption ?? 'Admin Upload',
                'status' => 'approved',
                'upload_status' => 'ready',
            ]);

            $roll->increment('current_photos');

            return back()->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => "Photo uploaded to R2/Storage successfully! URL: {$photoUrl}",
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::error('Admin photo upload error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('flash', [
                'toast' => [
                    'type' => 'danger',
                    'message' => 'Upload error: ' . $e->getMessage(),
                ],
            ])->withErrors([
                'upload_error' => $e->getMessage(),
            ]);
        }
    }

    public function destroyPhoto(FilmRoll $roll, Photo $photo): RedirectResponse
    {
        abort_unless($photo->film_roll_id === $roll->id, 404);

        $disk = config('filesystems.default', 's3');
        $storagePrefix = '/storage/';
        if (str_starts_with($photo->photo_url, $storagePrefix)) {
            Storage::disk('public')->delete(substr($photo->photo_url, strlen($storagePrefix)));
        } else {
            $baseUrl = rtrim((string) Storage::disk($disk)->url(''), '/');
            if (! empty($baseUrl) && str_starts_with($photo->photo_url, $baseUrl)) {
                $relativePath = ltrim(substr($photo->photo_url, strlen($baseUrl)), '/');
                if (! empty($relativePath)) {
                    Storage::disk($disk)->delete($relativePath);
                }
            }
        }

        $photo->delete();

        if ($roll->current_photos > 0) {
            $roll->decrement('current_photos');
        }

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Photo deleted successfully.',
            ],
        ]);
    }
}
