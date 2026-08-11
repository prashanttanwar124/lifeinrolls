<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CameraPreset;
use Illuminate\Http\JsonResponse;

class CameraPresetController extends Controller
{
    public function index(): JsonResponse
    {
        $presets = CameraPreset::where('is_active', true)->get()->map->toMobileProfile();

        return response()->json([
            'data' => $presets,
        ]);
    }

    public function show(CameraPreset $preset): JsonResponse
    {
        return response()->json([
            'data' => $preset->toMobileProfile(),
        ]);
    }
}
