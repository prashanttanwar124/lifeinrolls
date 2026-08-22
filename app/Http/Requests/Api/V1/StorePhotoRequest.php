<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => 'required|file|image|max:20480',
            'caption' => 'nullable|string|max:255',
            'camera_preset_id' => 'nullable|integer|exists:camera_presets,id',
        ];
    }
}
