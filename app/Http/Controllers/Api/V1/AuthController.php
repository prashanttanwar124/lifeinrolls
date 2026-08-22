<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use RespondsWithJson;

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'User registered successfully', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials provided.'],
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'Login successful');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            \App\Models\SupportRequest::create([
                'user_id' => $user->id,
                'subject' => 'Password Reset Request: ' . $user->email,
                'message' => 'Password reset requested via mobile app at ' . now()->toIso8601String(),
                'status' => 'open',
            ]);
        }

        return $this->success(
            null,
            'If an account is associated with this email, reset instructions have been sent.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Successfully logged out');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success([
            'user' => new UserResource($request->user()),
        ], 'Profile fetched');
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        // Delete user's uploaded photos from storage
        $photos = \App\Models\Photo::where('user_id', $user->id)->get();
        foreach ($photos as $photo) {
            if ($photo->photo_url) {
                try {
                    $path = parse_url($photo->photo_url, PHP_URL_PATH);
                    if ($path) {
                        $trimmed = ltrim($path, '/');
                        \Illuminate\Support\Facades\Storage::disk('s3')->delete($trimmed);
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Failed to delete photo on account deletion: {$e->getMessage()}");
                }
            }
            $photo->delete();
        }

        // Revoke all personal access tokens
        $user->tokens()->delete();

        // Delete user record (cascades to owned rolls & memberships)
        $user->delete();

        return $this->success(null, 'Your account and all associated data have been permanently deleted.');
    }
}
