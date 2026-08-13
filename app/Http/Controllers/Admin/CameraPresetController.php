<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CameraPreset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CameraPresetController extends Controller
{
    public function index(): Response
    {
        $presets = CameraPreset::latest()->get()->map(function ($preset) {
            $data = $preset->toArray();
            $data['lut_url'] = $preset->toMobileProfile()['lut_url'];

            return $data;
        });

        return Inertia::render('admin/Presets/Index', [
            'presets' => $presets,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'preset_key' => 'required|string|max:255|unique:camera_presets,preset_key',
            'name' => 'required|string|max:255',
            'version' => 'required|integer|min:1',
            'engine' => 'required|string|max:255',
            'lut' => 'nullable|string|max:255',
            'lut_file' => 'nullable|file|max:5120', // .cube LUT file up to 5MB
            'grain' => 'required|numeric|min:0|max:1',
            'bloom' => 'required|numeric|min:0|max:1',
            'vignette' => 'required|numeric|min:0|max:1',
            'aspect_ratio' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_premium' => 'boolean',
        ]);

        $lutPath = $validated['lut'] ?? "cameras/{$validated['preset_key']}.cube";
        $checksum = null;

        if ($request->hasFile('lut_file')) {
            $file = $request->file('lut_file');
            $checksum = hash_file('sha256', $file->getRealPath());
            $stored = $file->storeAs('cameras', "{$validated['preset_key']}.cube", 'public');
            $lutPath = $stored;
        } elseif (! empty($lutPath)) {
            $filePath = storage_path('app/public/'.ltrim($lutPath, '/'));
            if (file_exists($filePath)) {
                $checksum = hash_file('sha256', $filePath);
            }
        }

        CameraPreset::create([
            'preset_key' => $validated['preset_key'],
            'name' => $validated['name'],
            'version' => $validated['version'],
            'engine' => $validated['engine'],
            'lut' => $lutPath,
            'lut_checksum' => $checksum,
            'grain' => $validated['grain'],
            'bloom' => $validated['bloom'],
            'vignette' => $validated['vignette'],
            'aspect_ratio' => $validated['aspect_ratio'],
            'description' => $validated['description'] ?? null,
            'is_premium' => $validated['is_premium'] ?? false,
            'is_active' => true,
        ]);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Camera profile & LUT created successfully.',
            ],
        ]);
    }

    public function toggle(CameraPreset $preset): RedirectResponse
    {
        $preset->update([
            'is_active' => ! $preset->is_active,
        ]);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Preset status updated.',
            ],
        ]);
    }

    public function destroy(CameraPreset $preset): RedirectResponse
    {
        $preset->delete();

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Camera preset deleted.',
            ],
        ]);
    }
}
