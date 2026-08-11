<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilmRoll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RollController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        $rolls = FilmRoll::with(['creator', 'cameraPreset'])
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
}
