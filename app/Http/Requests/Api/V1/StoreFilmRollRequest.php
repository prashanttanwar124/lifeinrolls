<?php

namespace App\Http\Requests\Api\V1;

use App\Models\FilmRoll;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFilmRollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // The API accepts both `name`/`mode` (mobile app) and `title`/`roll_type` (legacy).
        $this->merge([
            'title' => $this->input('title', $this->input('name')),
            'roll_type' => $this->input('roll_type', $this->input('mode')),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|max:10240',
            'max_photos' => 'nullable|integer|min:1|max:100',
            'roll_type' => ['nullable', 'string', Rule::in(FilmRoll::MODES)],
            'camera_preset_id' => 'nullable|exists:camera_presets,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'reveal_at' => 'nullable|date',
        ];
    }
}
