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

    public function destroyPhoto(FilmRoll $roll, Photo $photo): RedirectResponse
    {
        abort_unless($photo->film_roll_id === $roll->id, 404);

        $storagePrefix = '/storage/';
        if (str_starts_with($photo->photo_url, $storagePrefix)) {
            Storage::disk('public')->delete(substr($photo->photo_url, strlen($storagePrefix)));
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
