<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Laravolt\Avatar\Facade as Avatar;

class AvatarController extends Controller
{
    /**
     * Upload user avatar
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048' // 2MB max
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.general.validation_failed'),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            $file = $request->file('avatar');

            // Delete old avatar if exists
            if ($user->avatar) {
                $this->deleteAvatarFiles($user->avatar);
            }

            // Generate unique filename
            $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Create image manager instance
            $manager = new ImageManager(new Driver());

            // Process and save different sizes
            $originalImage = $manager->read($file);

            // Original size (max 500x500)
            $originalImage->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Save original
            Storage::disk('public')->put(
                'avatars/' . $filename,
                $originalImage->encode()
            );

            // Create thumbnail (150x150)
            $thumbnail = clone $originalImage;
            $thumbnail->resize(150, 150, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            Storage::disk('public')->put(
                'avatars/thumbnails/' . $filename,
                $thumbnail->encode()
            );

            // Create small avatar (50x50)
            $small = clone $originalImage;
            $small->resize(50, 50, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            Storage::disk('public')->put(
                'avatars/small/' . $filename,
                $small->encode()
            );

            // Update user avatar
            $user->update(['avatar' => $filename]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.avatar.uploaded_successfully'),
                'data' => [
                    'avatar' => $filename,
                    'avatar_url' => Storage::disk('public')->url('avatars/' . $filename),
                    'thumbnail_url' => Storage::disk('public')->url('avatars/thumbnails/' . $filename),
                    'small_url' => Storage::disk('public')->url('avatars/small/' . $filename)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.avatar.upload_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user avatar
     */
    public function delete(): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->avatar) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.avatar.no_avatar_found')
                ], 404);
            }

            // Delete avatar files
            $this->deleteAvatarFiles($user->avatar);

            // Update user
            $user->update(['avatar' => null]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.avatar.deleted_successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.avatar.delete_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get avatar URL for a user
     */
    public function show(Request $request, $size = 'original'): JsonResponse
    {
        $user = auth()->user();

        if (!$user->avatar) {
            // Generate default avatar using Laravolt Avatar
            $defaultAvatar = $this->generateDefaultAvatar($user->name, $size);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'avatar' => null,
                    'avatar_url' => $defaultAvatar,
                    'is_default' => true
                ]
            ]);
        }

        $avatarPath = match($size) {
            'thumbnail' => 'avatars/thumbnails/' . $user->avatar,
            'small' => 'avatars/small/' . $user->avatar,
            default => 'avatars/' . $user->avatar
        };

        return response()->json([
            'status' => 'success',
            'data' => [
                'avatar' => $user->avatar,
                'avatar_url' => Storage::disk('public')->url($avatarPath),
                'is_default' => false
            ]
        ]);
    }

    /**
     * Delete avatar files from storage
     */
    private function deleteAvatarFiles(string $filename): void
    {
        $paths = [
            'avatars/' . $filename,
            'avatars/thumbnails/' . $filename,
            'avatars/small/' . $filename
        ];

        foreach ($paths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Generate default avatar using Laravolt Avatar
     */
    private function generateDefaultAvatar(string $name, string $size = 'original'): string
    {
        $avatarSize = match($size) {
            'thumbnail' => 150,
            'small' => 50,
            default => 500
        };

        // Generate avatar and save to storage
        $avatar = Avatar::create($name)->setDimension($avatarSize, $avatarSize);

        // Generate filename based on name and size
        $filename = 'default_' . md5($name) . '_' . $avatarSize . '.png';
        $path = 'avatars/defaults/' . $filename;

        // Save to storage if not exists
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $avatar->encode());
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Get initials from name (kept for backup)
     */
    private function getInitials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            if (strlen($initials) < 2 && !empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        return $initials ?: 'U';
    }
}
