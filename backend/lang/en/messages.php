<?php

return [
    // Authentication
    'auth' => [
        'failed' => 'These credentials do not match our records.',
        'password' => 'The provided password is incorrect.',
        'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
        'login_success' => 'Login successful',
        'logout_success' => 'Logout successful',
        'unauthorized' => 'You are not authorized to access this resource',
        'token_invalid' => 'Token is invalid',
        'token_expired' => 'Token has expired',
        'token_blacklisted' => 'Token has been blacklisted',
        'profile_updated' => 'Profile updated successfully',
        'password_changed' => 'Password changed successfully',
        'old_password_incorrect' => 'Current password is incorrect',
    ],

    // Users
    'users' => [
        'created' => 'User created successfully',
        'updated' => 'User updated successfully',
        'deleted' => 'User deleted successfully',
        'not_found' => 'User not found',
        'cannot_delete_self' => 'You cannot delete your own account',
        'cannot_delete_super_admin' => 'Super Admin cannot be deleted',
        'email_already_exists' => 'Email address already exists',
        'retrieved' => 'Users retrieved successfully',
        'user_retrieved' => 'User retrieved successfully',
    ],

    // Roles
    'roles' => [
        'created' => 'Role created successfully',
        'updated' => 'Role updated successfully', 
        'deleted' => 'Role deleted successfully',
        'not_found' => 'Role not found',
        'cannot_delete_assigned' => 'Cannot delete role that is assigned to users',
        'cannot_delete_system_role' => 'System roles cannot be deleted',
        'name_already_exists' => 'Role name already exists',
        'retrieved' => 'Roles retrieved successfully',
        'role_retrieved' => 'Role retrieved successfully',
        'permissions_updated' => 'Role permissions updated successfully',
    ],

    // Permissions
    'permissions' => [
        'retrieved' => 'Permissions retrieved successfully',
        'grouped_retrieved' => 'Grouped permissions retrieved successfully',
        'not_found' => 'Permission not found',
        'invalid_permission' => 'Invalid permission provided',
    ],

    // Dashboard
    'dashboard' => [
        'stats_retrieved' => 'Dashboard statistics retrieved successfully',
        'stats_error' => 'Failed to retrieve dashboard statistics',
    ],

    // Validation
    'validation' => [
        'required' => 'The :attribute field is required',
        'email' => 'The :attribute must be a valid email address',
        'unique' => 'The :attribute has already been taken',
        'min' => 'The :attribute must be at least :min characters',
        'max' => 'The :attribute must not be greater than :max characters',
        'confirmed' => 'The :attribute confirmation does not match',
        'same' => 'The :attribute and :other must match',
        'exists' => 'The selected :attribute is invalid',
        'array' => 'The :attribute must be an array',
        'string' => 'The :attribute must be a string',
        'numeric' => 'The :attribute must be a number',
        'boolean' => 'The :attribute field must be true or false',
    ],

    // General
    'general' => [
        'success' => 'Operation completed successfully',
        'error' => 'An error occurred',
        'not_found' => 'Resource not found',
        'forbidden' => 'You do not have permission to perform this action',
        'server_error' => 'Internal server error',
        'validation_failed' => 'Validation failed',
        'created' => 'Resource created successfully',
        'updated' => 'Resource updated successfully',
        'deleted' => 'Resource deleted successfully',
        'retrieved' => 'Resource retrieved successfully',
    ],

    // System
    'system' => [
        'maintenance_mode' => 'System is under maintenance',
        'feature_disabled' => 'This feature is currently disabled',
        'rate_limit_exceeded' => 'Rate limit exceeded. Please try again later',
        'api_endpoint_not_found' => 'API endpoint not found',
    ],
];
