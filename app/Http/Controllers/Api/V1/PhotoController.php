<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePhotoRequest;
use App\Http\Resources\Api\V1\PhotoResource;
use App\Models\FilmRoll;
use App\Models\Photo;
use App\Models\PhotoReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PhotoController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request, FilmRoll $roll): JsonResponse
    {
        Gate::authorize('viewPhotos', $roll);

        $user = $request->user();
        $isModerator = $roll->isOwnerOrAdmin($user);

        $query = $roll->photos()->with(['user', 'cameraPreset'])->latest();

        if (! $isModerator) {
            // Surprise rolls: before reveal, members only see their own uploads.
            if (! $roll->isRevealed()) {
                $query->where('user_id', $user->id);
            } else {
                // Approval rolls: unapproved photos are visible only to their uploader.
                $query->where(function ($q) use ($user) {
                    $q->where('status', 'approved')->orWhere('user_id', $user->id);
                });
            }
        }

        return $this->success(
            PhotoResource::collection($query->get()),
            'Photos fetched'
        );
    }

    public function store(StorePhotoRequest $request, FilmRoll $roll): JsonResponse
    {
        Gate::authorize('uploadPhoto', $roll);

        if ($roll->current_photos >= $roll->max_photos) {
            return $this->error('Film roll is full! No more exposures available.', 422);
        }

        try {
            $disk = config('filesystems.default', 's3');
            $path = $request->file('photo')->store("rolls/{$roll->id}", $disk);
            $photoUrl = Storage::disk($disk)->url($path);

            $photo = Photo::create([
                'film_roll_id' => $roll->id,
                'user_id' => $request->user()->id,
                'camera_preset_id' => $request->camera_preset_id ?? $roll->camera_preset_id,
                'photo_url' => $photoUrl,
                'thumbnail_url' => $photoUrl,
                'caption' => $request->caption,
                'status' => $roll->roll_type === 'approval' ? 'pending_approval' : 'approved',
                'upload_status' => 'ready',
            ]);

            $roll->increment('current_photos');

            if ($roll->refresh()->current_photos >= $roll->max_photos) {
                $roll->update(['status' => 'completed']);
            }

            return $this->success(
                new PhotoResource($photo->load(['user', 'cameraPreset'])),
                'Photo uploaded successfully',
                201
            );
        } catch (\Throwable $e) {
            \Log::error('API Photo upload exception: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->error('Failed to upload photo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the upload pipeline status (uploaded / processing / ready / failed).
     */
    public function updateStatus(Request $request, Photo $photo): JsonResponse
    {
        Gate::authorize('updateStatus', $photo);

        $validated = $request->validate([
            'upload_status' => ['required', 'string', Rule::in(['uploaded', 'processing', 'ready', 'failed'])],
        ]);

        $photo->update(['upload_status' => $validated['upload_status']]);

        return $this->success(new PhotoResource($photo->load('user')), 'Photo status updated');
    }

    public function destroy(Request $request, Photo $photo): JsonResponse
    {
        Gate::authorize('delete', $photo);

        $disk = config('filesystems.default', 's3');
        $storagePrefix = '/storage/';
        if (str_starts_with($photo->photo_url, $storagePrefix)) {
            Storage::disk('public')->delete(substr($photo->photo_url, strlen($storagePrefix)));
        } else {
            $baseUrl = rtrim((string) Storage::disk($disk)->url(''), '/');
            if (! empty($baseUrl) && str_starts_with($photo->photo_url, $baseUrl)) {
                $relativePath = ltrim(substr($photo->photo_url, strlen($baseUrl)), '/');
                if (! empty($relativePath)) {
                    Storage::disk($disk)->delete($relativePath);
                }
            }
        }

        $roll = $photo->filmRoll;
        $photo->delete();

        if ($roll->current_photos > 0) {
            $roll->decrement('current_photos');
        }

        return $this->success(null, 'Photo deleted');
    }

    public function approve(Request $request, Photo $photo): JsonResponse
    {
        Gate::authorize('moderate', $photo);

        $photo->update(['status' => 'approved']);

        return $this->success(new PhotoResource($photo->load('user')), 'Photo approved');
    }

    public function reject(Request $request, Photo $photo): JsonResponse
    {
        Gate::authorize('moderate', $photo);

        $photo->update(['status' => 'rejected']);

        return $this->success(new PhotoResource($photo->load('user')), 'Photo rejected');
    }

    public function report(Request $request, Photo $photo): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $report = PhotoReport::create([
            'photo_id' => $photo->id,
            'user_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return $this->success($report, 'Photo reported to administration', 201);
    }
}
