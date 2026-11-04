<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PermissionService
{
    public function create(array $data): Permission
    {
        return DB::transaction(fn () => Permission::create([
            'name' => $data['name'],
            'group' => $data['group'],
            'description' => $data['description'] ?? null,
        ]));
    }

    public function update(Permission $permission, array $data): Permission
    {
        return DB::transaction(function () use ($permission, $data) {
            $permission->update([
                'name' => $data['name'],
                'group' => $data['group'],
                'description' => $data['description'] ?? null,
            ]);

            return $permission;
        });
    }

    public function delete(Permission $permission): void
    {
        if ($permission->roles()->count() > 0) {
            throw ValidationException::withMessages([
                'permission' => __('permissions.has_roles_hint'),
            ]);
        }

        DB::transaction(fn () => $permission->delete());
    }
}