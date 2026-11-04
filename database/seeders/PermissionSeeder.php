<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['dashboard.view', 'dashboard', 'Access the admin dashboard'],

            ['users.view', 'users', 'View users'],
            ['users.create', 'users', 'Create users'],
            ['users.edit', 'users', 'Edit users'],
            ['users.delete', 'users', 'Delete users'],
            ['users.assign_roles', 'users', 'Assign roles to users'],

            ['roles.view', 'roles', 'View roles'],
            ['roles.create', 'roles', 'Create roles'],
            ['roles.edit', 'roles', 'Edit roles'],
            ['roles.delete', 'roles', 'Delete roles'],
            ['roles.assign_permissions', 'roles', 'Assign permissions to roles'],

            ['permissions.view', 'permissions', 'View permissions'],
            ['permissions.create', 'permissions', 'Create permissions'],
            ['permissions.edit', 'permissions', 'Edit permissions'],
            ['permissions.delete', 'permissions', 'Delete permissions'],

            ['imports.view', 'imports', 'View import batches'],
            ['imports.upload', 'imports', 'Upload Excel files'],
            ['imports.preview', 'imports', 'Preview Excel content'],
            ['imports.execute', 'imports', 'Run an import'],
            ['imports.delete', 'imports', 'Delete import batches'],

            ['data.view', 'data', 'View data records'],
            ['data.create', 'data', 'Create data records'],
            ['data.edit', 'data', 'Edit data records'],
            ['data.delete', 'data', 'Delete data records'],

            ['reports.view', 'reports', 'View reports'],
            ['reports.export', 'reports', 'Export reports'],

            ['settings.manage', 'settings', 'Manage system settings'],
            ['activity_logs.view', 'activity_logs', 'View activity logs'],

            ['units.view', 'units', 'View units'],
            ['units.create', 'units', 'Create units'],
            ['units.edit', 'units', 'Edit units'],
            ['units.delete', 'units', 'Delete units'],
        ];

        foreach ($permissions as [$name, $group, $description]) {
            Permission::updateOrCreate(
                ['name' => $name],
                ['group' => $group, 'description' => $description]
            );
        }
    }
}