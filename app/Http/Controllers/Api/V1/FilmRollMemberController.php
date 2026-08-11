<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateMemberRoleRequest;
use App\Http\Resources\Api\V1\FilmRollMemberResource;
use App\Models\FilmRoll;
use App\Models\FilmRollMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class FilmRollMemberController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request, FilmRoll $roll): JsonResponse
    {
        Gate::authorize('viewPhotos', $roll); // members only

        $members = $roll->memberships()->with('user')->orderBy('joined_at')->get();

        return $this->success(
            FilmRollMemberResource::collection($members),
            'Members fetched'
        );
    }

    /**
     * Return (or lazily create) the roll's invite code and shareable link.
     */
    public function invite(Request $request, FilmRoll $roll): JsonResponse
    {
        Gate::authorize('invite', $roll);

        if ($roll->invite_token === null) {
            $roll->update(['invite_token' => Str::random(40)]);
        }

        return $this->success([
            'invite_code' => $roll->invite_code,
            'invite_token' => $roll->invite_token,
            'invite_link' => url('/join/'.$roll->invite_token),
        ], 'Invite details fetched');
    }

    /**
     * Rotate the invite code and link, invalidating previously shared ones.
     */
    public function regenerateInvite(Request $request, FilmRoll $roll): JsonResponse
    {
        Gate::authorize('invite', $roll);

        $roll->update([
            'invite_code' => strtoupper(Str::random(8)),
            'invite_token' => Str::random(40),
        ]);

        return $this->success([
            'invite_code' => $roll->invite_code,
            'invite_token' => $roll->invite_token,
            'invite_link' => url('/join/'.$roll->invite_token),
        ], 'Invite regenerated');
    }

    public function update(UpdateMemberRoleRequest $request, FilmRoll $roll, FilmRollMember $member): JsonResponse
    {
        Gate::authorize('manageMembers', $roll);

        if ($member->film_roll_id !== $roll->id) {
            return $this->error('Member does not belong to this roll.', 404);
        }

        if ($member->role === 'owner') {
            return $this->error('The owner role cannot be changed.', 422);
        }

        $member->update(['role' => $request->validated()['role']]);
        $member->load('user');

        return $this->success(new FilmRollMemberResource($member), 'Member role updated');
    }

    public function destroy(Request $request, FilmRoll $roll, FilmRollMember $member): JsonResponse
    {
        Gate::authorize('manageMembers', $roll);

        if ($member->film_roll_id !== $roll->id) {
            return $this->error('Member does not belong to this roll.', 404);
        }

        if ($member->role === 'owner') {
            return $this->error('The roll owner cannot be removed.', 422);
        }

        $member->delete();

        return $this->success(null, 'Member removed');
    }
}
