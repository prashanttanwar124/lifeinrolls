<?php

namespace App\Policies;

use App\Models\Photo;
use App\Models\User;

class PhotoPolicy
{
    public function view(User $user, Photo $photo): bool
    {
        $roll = $photo->filmRoll;

        if (! $roll->isMember($user)) {
            return false;
        }

        if ($photo->user_id === $user->id || $roll->isOwnerOrAdmin($user)) {
            return true;
        }

        if (! $roll->isRevealed()) {
            return false;
        }

        return $photo->status === 'approved';
    }

    public function delete(User $user, Photo $photo): bool
    {
        return $photo->user_id === $user->id
            || $photo->filmRoll->isOwnerOrAdmin($user);
    }

    /**
     * Uploader may update the upload pipeline status of their own photo.
     */
    public function updateStatus(User $user, Photo $photo): bool
    {
        return $photo->user_id === $user->id;
    }

    public function moderate(User $user, Photo $photo): bool
    {
        return $photo->filmRoll->isOwnerOrAdmin($user);
    }
}
