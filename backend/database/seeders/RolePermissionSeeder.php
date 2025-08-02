<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

            // System
            'manage_settings',
            'view_logs',
            'system_admin',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin Role (all permissions)
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Owner Role (high-level management)
        $ownerRole = Role::create(['name' => 'Owner']);
        $ownerRole->givePermissionTo([
            'view_users',
            'create_users',
            'edit_users',
            'view_roles',
            'assign_roles',
            'view_dashboard',
            'view_analytics',
            'view_profile',
            'edit_profile',
        ]);

        // User Role (basic access)
        $userRole = Role::create(['name' => 'User']);
        $userRole->givePermissionTo([
            'view_dashboard',
            'view_profile',
            'edit_profile',
        ]);

        // Create Super Admin user
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@dashboard.com',
            'password' => bcrypt('SuperAdmin123!'),
            'email_verified_at' => now(),
        ]);

        $superAdmin->assignRole('Super Admin');

        // Create sample Owner user
        $owner = User::create([
            'name' => 'John Owner',
            'email' => 'owner@dashboard.com',
            'password' => bcrypt('Owner123!'),
            'email_verified_at' => now(),
        ]);

        $owner->assignRole('Owner');

        // Create sample regular user
        $user = User::create([
            'name' => 'Jane User',
            'email' => 'user@dashboard.com',
            'password' => bcrypt('User123!'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('User');

        $this->command->info('Roles, permissions, and default users created successfully!');
        $this->command->info('Super Admin: admin@dashboard.com / SuperAdmin123!');
        $this->command->info('Owner: owner@dashboard.com / Owner123!');
        $this->command->info('User: user@dashboard.com / User123!');
    }
}
