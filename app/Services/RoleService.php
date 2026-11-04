<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            if (! empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    public function updateRole(Role $role, array $data): Role
    {
        if ($role->is_protected && $role->name !== $data['name']) {
            throw ValidationException::withMessages([
                'name' => __('roles.name_locked'),
            ]);
        }

        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $role->syncPermissions($data['permissions'] ?? []);

            return $role;
        });
    }

    public function deleteRole(Role $role): void
    {
        if ($role->is_protected) {
            throw ValidationException::withMessages([
                'role' => __('roles.protected_hint'),
            ]);
        }

        if ($role->users()->count() > 0) {
            throw ValidationException::withMessages([
                'role' => __('roles.has_users_hint'),
            ]);
        }

        DB::transaction(fn () => $role->delete());
    }
}