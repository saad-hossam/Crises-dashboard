<?php

return [
    // ── Page titles ─────────────────────────────────────────
    'title' => 'Permissions',
    'create' => 'Create Permission',
    'edit' => 'Edit Permission',
    'show' => 'Permission Details',
    'details' => 'Permission Details',

    // ── Listing ─────────────────────────────────────────────
    'total' => ':count permissions',
    'info' => 'Permissions are managed here. Sidebar visibility, route gates, and @can directives update automatically.',
    'search_placeholder' => 'Search by name or description...',
    'all_groups' => 'All groups',
    'ungrouped' => 'Ungrouped',
    'no_data' => 'No permissions found.',
    'roles_count' => 'Roles',
    'roles_with_permission' => 'Roles with this permission',
    'no_roles' => 'No roles have this permission yet.',

    // ── Fields ──────────────────────────────────────────────
    'name' => 'Permission Name',
    'name_hint' => 'Lowercase with dots, e.g. reports.export or users.impersonate.',
    'name_placeholder' => 'e.g. reports.export',
    'group' => 'Group',
    'group_hint' => 'Groups permissions in the UI, e.g. reports.',
    'description' => 'Description',
    'description_placeholder' => 'Optional: what does this permission allow?',
    'guard' => 'Guard',
    'created_at' => 'Created',

    // ── Warnings ────────────────────────────────────────────
    'rename_warning' => 'Renaming a permission will immediately change its effect in all @can(...) checks.',
    'has_roles_hint' => 'Remove this permission from all roles before deleting it.',
    'delete_confirm' => 'Delete this permission?',

    // ── Flash messages ──────────────────────────────────────
    'created' => 'Permission created successfully.',
    'updated' => 'Permission updated successfully.',
    'deleted' => 'Permission deleted successfully.',

    // ── Group labels ────────────────────────────────────────
    'groups' => [
        'dashboard'     => 'Dashboard',
        'users'         => 'Users',
        'roles'         => 'Roles',
        'permissions'   => 'Permissions',
        'imports'       => 'Imports',
        'data'          => 'Data Management',
        'reports'       => 'Reports',
        'settings'      => 'Settings',
        'activity_logs' => 'Activity Logs',
        'units'         => 'Units',
    ],

    // ── Validation messages ─────────────────────────────────
    'validation' => [
        'name_required'  => 'Permission name is required.',
        'name_unique'    => 'This permission already exists.',
        'name_regex'     => 'Name must be lowercase alphanumerics/underscores, optionally with one dot (e.g. reports.export).',
        'group_required' => 'Permission group is required.',
    ],
];