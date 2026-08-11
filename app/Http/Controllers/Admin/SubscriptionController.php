<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(): Response
    {
        $plans = SubscriptionPlan::latest()->get();

        return Inertia::render('admin/Subscriptions/Index', [
            'plans' => $plans,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|string|in:monthly,yearly',
            'max_rolls' => 'required|integer|min:1',
            'max_photos_per_roll' => 'required|integer|min:1',
            'allows_custom_presets' => 'boolean',
        ]);

        SubscriptionPlan::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'price' => $validated['price'],
            'currency' => 'USD',
            'interval' => $validated['interval'],
            'max_rolls' => $validated['max_rolls'],
            'max_photos_per_roll' => $validated['max_photos_per_roll'],
            'allows_custom_presets' => $validated['allows_custom_presets'] ?? false,
            'is_active' => true,
        ]);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Subscription plan created.',
            ],
        ]);
    }
}
