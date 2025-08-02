<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index()
    {
        try {
            $permissions = Permission::all(['id', 'name', 'guard_name']);
            
            return response()->json([
                'success' => true,
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display permissions grouped by category
     */
    public function grouped()
    {
        try {
            $permissions = Permission::all(['id', 'name', 'guard_name']);
            
            // Group permissions by category based on naming convention
            $grouped = [
                'user_management' => [
                    'label' => 'User Management',
                    'description' => 'Manage user accounts and profiles',
                    'permissions' => $permissions->filter(fn($p) => 
                        str_contains($p->name, 'user') || 
                        in_array($p->name, ['manage_users'])
                    )->values()
                ],
                'role_management' => [
                    'label' => 'Role Management',
                    'description' => 'Manage roles and permissions',
                    'permissions' => $permissions->filter(fn($p) => 
                        str_contains($p->name, 'role') || 
                        str_contains($p->name, 'permission') ||
                        in_array($p->name, ['assign_roles'])
                    )->values()
                ],
                'system_access' => [
                    'label' => 'System Access',
                    'description' => 'Access to system features and data',
                    'permissions' => $permissions->filter(fn($p) => 
                        in_array($p->name, ['view_dashboard', 'view_analytics', 'view_profile', 'edit_profile', 'view_logs'])
                    )->values()
                ],
                'administration' => [
                    'label' => 'Administration',
                    'description' => 'System administration and settings',
                    'permissions' => $permissions->filter(fn($p) => 
                        in_array($p->name, ['manage_permissions', 'manage_settings', 'system_admin'])
                    )->values()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $grouped
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch grouped permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
