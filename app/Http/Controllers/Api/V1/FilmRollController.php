<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\JoinFilmRollRequest;
use App\Http\Requests\Api\V1\StoreFilmRollRequest;
use App\Http\Requests\Api\V1\UpdateFilmRollRequest;
use App\Http\Resources\Api\V1\FilmRollResource;
use App\Models\FilmRoll;
use App\Models\FilmRollMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class FilmRollController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $rolls = $request->user()
            ->filmRolls()
            ->whereNull('archived_at')
            ->with(['cameraPreset', 'creator'])
            ->withCount(['memberships', 'photos'])
            ->latest('film_rolls.created_at')
            ->get();

        return $this->success(
            FilmRollResource::collection($rolls),
            'Rolls fetched'
        );
    }

    public function store(StoreFilmRollRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = '/storage/'.$request->file('cover_image')->store('covers', 'public');
        }

        $roll = FilmRoll::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'cover_image' => $coverPath,
            'invite_code' => strtoupper(Str::random(8)),
            'invite_token' => Str::random(40),
            'max_photos' => $validated['max_photos'] ?? 36,
            'current_photos' => 0,
            'roll_type' => $validated['roll_type'] ?? 'standard',
            'status' => 'active',
            'camera_preset_id' => $validated['camera_preset_id'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'reveal_at' => $validated['reveal_at'] ?? null,
        ]);

        FilmRollMember::create([
            'film_roll_id' => $roll->id,
            'user_id' => $request->user()->id,
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        $roll->load(['cameraPreset', 'creator'])->loadCount(['memberships', 'photos']);

        return $this->success(
            new FilmRollResource($roll),
            'Roll created successfully',
            201
        );
    }

    public function show(Request $request, FilmRoll $roll): JsonResponse
    {
        Gate::authorize('view', $roll);

        $roll->load(['cameraPreset', 'creator'])->loadCount(['memberships', 'photos']);

        return $this->success(new FilmRollResource($roll), 'Roll fetched');
    }

    public function update(UpdateFilmRollRequest $request, FilmRoll $roll): JsonResponse
    {
        Gate::authorize('update', $roll);

        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = '/storage/'.$request->file('cover_image')->store('covers', 'public');
        }

        $roll->update($validated);
        $roll->load(['cameraPreset', 'creator'])->loadCount(['memberships', 'photos']);

        return $this->success(new FilmRollResource($roll), 'Roll updated successfully');
    }

    /**
     * Archive the roll (soft archive — it disappears from listings but data is kept).
     */
    public function destroy(Request $request, FilmRoll $roll): JsonResponse
    {
        Gate::authorize('delete', $roll);

        $roll->update(['archived_at' => now(), 'status' => 'archived']);

        return $this->success(null, 'Roll archived successfully');
    }

    public function join(JoinFilmRollRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $roll = FilmRoll::query()
            ->when(isset($validated['invite_code']),
                fn ($q) => $q->where('invite_code', strtoupper($validated['invite_code'])))
            ->when(! isset($validated['invite_code']) && isset($validated['invite_token']),
                fn ($q) => $q->where('invite_token', $validated['invite_token']))
            ->whereNull('archived_at')
            ->first();

        if ($roll === null) {
            return $this->error('Invalid invite code.', 404);
        }

        $existing = FilmRollMember::where('film_roll_id', $roll->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $existing) {
            FilmRollMember::create([
                'film_roll_id' => $roll->id,
                'user_id' => $request->user()->id,
                'role' => 'contributor',
                'joined_at' => now(),
            ]);
        }

        $roll->load(['cameraPreset', 'creator'])->loadCount(['memberships', 'photos']);

        return $this->success(
            new FilmRollResource($roll),
            $existing ? 'You are already a member of this roll.' : 'Successfully joined roll'
        );
    }
}
