<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permissions — English
    |--------------------------------------------------------------------------
    */

    // Titles
    'title'                     => 'Permissions',
    'permission'                => 'Permission',
    'create'                    => 'Create Permission',
    'edit'                      => 'Edit Permission',
    'show'                      => 'Permission Details',
    'details'                   => 'Details',

    // Fields
    'name'                      => 'Name',
    'name_hint'                 => 'Use a dotted format like: reports.export (lowercase only).',
    'group'                     => 'Group',
    'group_hint'                => 'e.g. reports, users, settings.',
    'description'               => 'Description',
    'description_placeholder'   => 'A short description of the permission (optional)',
    'created_at'                => 'Created At',
    'roles_count'               => 'Roles',
    'search_placeholder'        => 'Search by permission name...',
    'all_groups'                => 'All Groups',
    'ungrouped'                 => 'Ungrouped',
    'roles_with_permission'     => 'Roles with this permission',

    // Messages
    'created_successfully'      => 'Permission created successfully.',
    'updated_successfully'      => 'Permission updated successfully.',
    'deleted_successfully'      => 'Permission deleted successfully.',
    'delete_confirm'            => 'Are you sure you want to delete this permission?',
    'has_roles_hint'            => 'Cannot delete a permission assigned to roles.',
    'no_roles'                  => 'No roles are assigned to this permission.',

    // Group labels
    'groups' => [
        'dashboard'     => 'Dashboard',
        'users'         => 'Users',
        'roles'         => 'Roles',
        'permissions'   => 'Permissions',
        'imports'       => 'Imports',
        'data'          => 'Data',
        'reports'       => 'Reports',
        'settings'      => 'Settings',
        'activity_logs' => 'Activity Logs',
        'units'         => 'Units',
    ],
];