<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Laravolt\Avatar\Facade as Avatar;

class UserController extends Controller
{
    /**
     * Generate avatar URL for user
     */
    private function generateAvatarUrl($user)
    {
        // If user has avatar file and it exists in storage
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            $avatarPath = Storage::disk('public')->path($user->avatar);
            $imageData = base64_encode(file_get_contents($avatarPath));
            $mimeType = mime_content_type($avatarPath);
            return 'data:' . $mimeType . ';base64,' . $imageData;
        }

        // Generate avatar from initials using Laravolt Avatar
        $avatarUrl = Avatar::create($user->name)
            ->setDimension(200, 200)
            ->setFontSize(80)
            ->setBorder(0, '#ffffff')
            ->setBackground('#667eea')
            ->setForeground('#ffffff')
            ->toBase64();

        return $avatarUrl;
    }    /**
     * Display a listing of users with pagination and search
     */
    public function index(Request $request)
    {
        // Check permission
        if (!$request->user()->can('view_users')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $query = User::with('roles');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && !empty($request->role)) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->paginate($request->per_page ?? 15);

        // Transform users data to include avatar URL
        $users->getCollection()->transform(function ($user) {
            $user->avatar = $this->generateAvatarUrl($user);
            return $user;
        });

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Check permission
        if (!$request->user()->can('create_users')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole($request->roles);

        // Load relationships and add avatar URL
        $user->load('roles');
        $user->avatar = $this->generateAvatarUrl($user);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, User $user)
    {
        // Check permission
        if (!$request->user()->can('view_users')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $user->load('roles', 'permissions');

        // Add avatar URL
        $user->avatar = $this->generateAvatarUrl($user);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Check permission
        if (!$request->user()->can('edit_users')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Prevent editing Super Admin by non-Super Admin
        if ($user->isSuperAdmin() && !$request->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit Super Admin account'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only('name', 'email'));
        $user->syncRoles($request->roles);

        // Load relationships and add avatar URL
        $user->load('roles');
        $user->avatar = $this->generateAvatarUrl($user);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(Request $request, User $user)
    {
        // Check permission
        if (!$request->user()->can('delete_users')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Prevent deleting Super Admin
        if ($user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete Super Admin account'
            ], 403);
        }

        // Prevent self-deletion
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
