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
            'photo' => 'required|image|max:10240',
            'caption' => 'nullable|string|max:255',
            'camera_preset_id' => 'nullable|exists:camera_presets,id',
        ];
    }
}
