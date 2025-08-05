<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_users',

            // Role Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'assign_roles',

            // Permission Management
            'view_permissions',
            'manage_permissions',

            // Dashboard
            'view_dashboard',
            'view_analytics',

            // Profile
            'view_profile',
            'edit_profile',
            'upload_avatar',
            'delete_avatar',

            // System
            'manage_settings',
            'view_logs',
            'system_admin',

            // Advanced Features (Phase 6+)
            'view_activity_logs',
            'manage_activity_logs',
            'bulk_operations',
            'export_data',
            'import_data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin Role (ALL permissions - automatically gets any new permissions)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all()); // Sync ensures Super Admin ALWAYS has ALL permissions

        // Owner Role (high-level management)
        $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
        $ownerRole->syncPermissions([
            'view_users',
            'create_users',
            'edit_users',
            'view_roles',
            'assign_roles',
            'view_dashboard',
            'view_analytics',
            'view_profile',
            'edit_profile',
            'upload_avatar',
            'delete_avatar',
            'view_activity_logs',
            'export_data',
        ]);

        // User Role (basic access)
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $userRole->syncPermissions([
            'view_dashboard',
            'view_profile',
            'edit_profile',
            'upload_avatar',
            'delete_avatar',
        ]);

        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@dashboard.com'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('SuperAdmin123!'),
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->syncRoles(['Super Admin']);

        // Create sample Owner user
        $owner = User::firstOrCreate(
            ['email' => 'owner@dashboard.com'],
            [
                'name' => 'John Owner',
                'password' => bcrypt('Owner123!'),
                'email_verified_at' => now(),
            ]
        );

        $owner->syncRoles(['Owner']);

        // Create sample regular user
        $user = User::firstOrCreate(
            ['email' => 'user@dashboard.com'],
            [
                'name' => 'Jane User',
                'password' => bcrypt('User123!'),
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles(['User']);

        $this->command->info('Roles, permissions, and default users created successfully!');
        $this->command->info('Super Admin: admin@dashboard.com / SuperAdmin123!');
        $this->command->info('Owner: owner@dashboard.com / Owner123!');
        $this->command->info('User: user@dashboard.com / User123!');
    }
}
