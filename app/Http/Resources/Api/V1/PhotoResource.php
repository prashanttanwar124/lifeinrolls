<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'film_roll_id' => $this->film_roll_id,
            'photo_url' => $this->absoluteUrl($this->photo_url),
            'thumbnail_url' => $this->absoluteUrl($this->thumbnail_url),
            'caption' => $this->caption,
            'status' => $this->status,
            'upload_status' => $this->upload_status,
            'uploader' => new UserResource($this->whenLoaded('user')),
            'camera_preset_id' => $this->camera_preset_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    private function absoluteUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : url($path);
    }
}
