<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        // Generate simple initials-based avatar using SVG
        $initials = $this->getInitials($user->name);
        $backgroundColor = $this->generateColorFromString($user->email);
        
        $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
            <rect width="200" height="200" fill="' . $backgroundColor . '"/>
            <text x="100" y="125" font-family="Arial, sans-serif" font-size="80" font-weight="bold" 
                  text-anchor="middle" fill="white">' . $initials . '</text>
        </svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Get initials from name
     */
    private function getInitials($name)
    {
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        
        return $initials ?: 'U';
    }

    /**
     * Generate color from string
     */
    private function generateColorFromString($string)
    {
        $hash = md5($string);
        $colors = [
            '#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd',
            '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf',
            '#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe',
            '#43e97b', '#fa709a', '#fee140', '#a8edea', '#d299c2'
        ];
        
        $index = hexdec(substr($hash, 0, 2)) % count($colors);
        return $colors[$index];
    }
}
