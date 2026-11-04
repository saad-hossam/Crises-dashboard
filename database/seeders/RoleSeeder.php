<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Full system access', 'is_protected' => true],
            ['name' => 'Admin',       'description' => 'Administrator',        'is_protected' => false],
            ['name' => 'Manager',     'description' => 'Manager',              'is_protected' => false],
            ['name' => 'Data Entry',  'description' => 'Data entry operator',  'is_protected' => false],
            ['name' => 'Viewer',      'description' => 'Read-only viewer',     'is_protected' => false],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['name' => $roleData['name']], $roleData);
        }

        // Admin: all except settings + activity_logs
        $admin = Role::where('name', 'Admin')->first();
        $admin->syncPermissions(
            Permission::whereNotIn('name', ['settings.manage', 'activity_logs.view'])
                ->pluck('name')->all()
        );

        Role::where('name', 'Manager')->first()->syncPermissions([
            'dashboard.view', 'imports.view', 'imports.preview', 'imports.execute',
            'data.view', 'data.create', 'data.edit',
            'reports.view', 'reports.export',
            'units.view',
        ]);

        Role::where('name', 'Data Entry')->first()->syncPermissions([
            'dashboard.view', 'imports.view', 'imports.upload', 'imports.preview',
            'data.view', 'data.create',
            'units.view',
        ]);

        Role::where('name', 'Viewer')->first()->syncPermissions([
            'dashboard.view', 'imports.view', 'data.view', 'reports.view', 'units.view',
        ]);
    }
}