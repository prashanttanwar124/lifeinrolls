<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    public function index(): Response
    {
        $banners = Banner::latest()->get();

        return Inertia::render('admin/Banners/Index', [
            'banners' => $banners,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'nullable|string',
        ]);

        Banner::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_role' => $validated['target_role'] ?? 'all',
            'is_active' => true,
            'published_at' => now(),
        ]);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Banner published successfully.',
            ],
        ]);
    }
}
