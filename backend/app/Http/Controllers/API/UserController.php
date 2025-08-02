<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

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
            'phone' => 'nullable|string|max:20',
            'timezone' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:500',
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
            'phone' => $request->phone,
            'timezone' => $request->timezone,
            'bio' => $request->bio,
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
            'phone' => 'nullable|string|max:20',
            'timezone' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:500',
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

        // Update user data (excluding avatar - avatar is managed through Profile page only)
        $user->update($request->only('name', 'email', 'phone', 'timezone', 'bio'));
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
