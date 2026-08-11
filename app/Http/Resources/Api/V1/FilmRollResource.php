<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmRollResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $userRole = $user !== null ? $this->roleFor($user) : null;

        return [
            'id' => $this->id,
            'name' => $this->title,
            'description' => $this->description,
            'cover_image' => $this->cover_image !== null ? url($this->cover_image) : null,
            'owner' => new UserResource($this->whenLoaded('creator')),
            'mode' => $this->roll_type,
            'status' => $this->status,
            'start_date' => $this->starts_at?->toIso8601String(),
            'end_date' => $this->ends_at?->toIso8601String(),
            'reveal_at' => $this->reveal_at?->toIso8601String(),
            'is_revealed' => $this->isRevealed(),
            'max_photos' => $this->max_photos,
            'current_photos' => $this->current_photos,
            'member_count' => $this->whenCounted('memberships', fn () => $this->memberships_count, $this->memberships()->count()),
            'photo_count' => $this->whenCounted('photos', fn () => $this->photos_count, $this->photos()->count()),
            'user_role' => $userRole,
            'camera_preset_id' => $this->camera_preset_id,
            // Invite credentials are only exposed to the owner/admin.
            'invite_code' => $this->when(in_array($userRole, ['owner', 'admin'], true), $this->invite_code),
            'invite_link' => $this->when(
                in_array($userRole, ['owner', 'admin'], true),
                fn () => url('/join/'.$this->invite_token),
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
