<?php

namespace App\Http\Requests\Api\V1;

use App\Models\FilmRoll;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFilmRollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name') && ! $this->has('title')) {
            $this->merge(['title' => $this->input('name')]);
        }

        if ($this->has('mode') && ! $this->has('roll_type')) {
            $this->merge(['roll_type' => $this->input('mode')]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|max:10240',
            'max_photos' => 'sometimes|integer|min:1|max:100',
            'roll_type' => ['sometimes', 'string', Rule::in(FilmRoll::MODES)],
            'camera_preset_id' => 'nullable|exists:camera_presets,id',
            'status' => ['sometimes', 'string', Rule::in(['active', 'locked', 'completed', 'archived'])],
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'reveal_at' => 'nullable|date',
        ];
    }
}
