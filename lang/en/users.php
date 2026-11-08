<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Users — English
    |--------------------------------------------------------------------------
    */

    // Titles
    'title'                  => 'Users',
    'user'                   => 'User',
    'create'                 => 'Create User',
    'create_subtitle'        => 'Add a new user and assign their role and permissions.',
    'edit'                   => 'Edit User',
    'show'                   => 'User Details',

    // Sections
    'basic_info'             => 'Basic Information',
    'credentials'            => 'Login Credentials',
    'change_password'        => 'Change Password',
    'roles'                  => 'Roles',
    'assigned_roles'         => 'Assigned Roles',
    'effective_permissions'  => 'Effective Permissions',

    // Fields
    'name'                   => 'Name',
    'email'                  => 'Email Address',
    'phone'                  => 'Phone Number',
    'status'                 => 'Status',
    'password'               => 'Password',
    'password_confirm'       => 'Confirm Password',
    'new_password'           => 'New Password',
    'password_hint'          => 'At least 8 characters, preferably mixing letters, numbers, and symbols.',
    'password_optional_hint' => 'Leave blank if you do not want to change the password.',
    'created_at'             => 'Created At',
    'last_login'             => 'Last Login',
    'permissions_count'      => 'permissions',

    // Statuses
    'active'                 => 'Active',
    'inactive'               => 'Inactive',

    // Filters
    'search_placeholder'     => 'Search by name, email, or phone...',
    'all_statuses'           => 'All Statuses',
    'all_roles'              => 'All Roles',

    // Messages
    'created_successfully'   => 'User created successfully.',
    'updated_successfully'   => 'User updated successfully.',
    'deleted_successfully'   => 'User deleted successfully.',
    'delete_confirm'         => 'Are you sure you want to delete user ":name"?',
    'cannot_delete_self'     => 'You cannot delete your own account.',
    'no_roles_defined'       => 'No roles have been defined yet.',
    'no_roles_assigned'      => 'This user has not been assigned any role yet.',
    'no_permissions_assigned'=> 'This user has no effective permissions.',
];