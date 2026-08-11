<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class JoinFilmRollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invite_code' => 'required_without:invite_token|string|max:32',
            'invite_token' => 'required_without:invite_code|string|max:64',
        ];
    }
}
