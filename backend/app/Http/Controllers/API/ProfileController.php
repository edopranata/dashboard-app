<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravolt\Avatar\Facade as Avatar;

class ProfileController extends Controller
{
    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'phone' => 'nullable|string|max:20',
            'timezone' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation.validation_failed'),
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $user->update($request->only(['name', 'email', 'phone', 'timezone', 'bio']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $this->generateAvatarUrl($user),
                'phone' => $user->phone,
                'timezone' => $user->timezone,
                'bio' => $user->bio,
                'email_verified_at' => $user->email_verified_at,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]
        ]);
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,webp|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation.validation_failed'),
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        try {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');

            // Update user avatar path
            $user->update(['avatar' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'data' => [
                    'avatar' => $this->generateAvatarUrl($user)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user avatar
     */
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        try {
            // Delete avatar file if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Clear avatar from database
            $user->update(['avatar' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar deleted successfully',
                'data' => [
                    'avatar' => $this->generateAvatarUrl($user)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation.validation_failed'),
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Generate avatar URL - either from uploaded file or dynamic generation
     */
    private function generateAvatarUrl($user)
    {
        // If user has uploaded avatar, convert to base64
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            $avatarPath = Storage::disk('public')->path($user->avatar);
            $imageData = file_get_contents($avatarPath);
            $base64 = base64_encode($imageData);
            $mimeType = mime_content_type($avatarPath);
            return 'data:' . $mimeType . ';base64,' . $base64;
        }

        // Generate dynamic avatar from initials
        $avatar = Avatar::create($user->name)
            ->setDimension(200, 200)
            ->setFontSize(80)
            ->setBackground('#' . substr(hash('sha256', $user->email), 0, 6))
            ->toBase64();

        return 'data:image/svg+xml;base64,' . $avatar;
    }
}
