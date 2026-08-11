<?php

namespace App\Policies;

use App\Models\FilmRoll;
use App\Models\User;

class FilmRollPolicy
{
    /**
     * Members can always view. Non-members may view only non-private, non-surprise
     * roll details (e.g. after following an invite link).
     */
    public function view(User $user, FilmRoll $roll): bool
    {
        if ($roll->isMember($user)) {
            return true;
        }

        return ! in_array($roll->roll_type, ['private', 'surprise'], true);
    }

    public function update(User $user, FilmRoll $roll): bool
    {
        return $roll->isOwnerOrAdmin($user);
    }

    public function delete(User $user, FilmRoll $roll): bool
    {
        return $roll->roleFor($user) === 'owner';
    }

    public function manageMembers(User $user, FilmRoll $roll): bool
    {
        return $roll->isOwnerOrAdmin($user);
    }

    public function invite(User $user, FilmRoll $roll): bool
    {
        return $roll->isOwnerOrAdmin($user);
    }

    public function viewPhotos(User $user, FilmRoll $roll): bool
    {
        return $roll->isMember($user);
    }

    public function uploadPhoto(User $user, FilmRoll $roll): bool
    {
        return $roll->canContribute($user);
    }

    public function moderatePhotos(User $user, FilmRoll $roll): bool
    {
        return $roll->isOwnerOrAdmin($user);
    }
}
