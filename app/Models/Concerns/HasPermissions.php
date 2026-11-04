<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    /**
     * Short in-request cache so multiple can() calls don't hit the DB repeatedly.
     */
    protected ?array $cachedPermissionNames = null;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Effective permission names for this user, from all their roles,
     * plus (if you later add per-user overrides) direct user permissions.
     */
    public function permissionNames(): array
    {
        if ($this->cachedPermissionNames !== null) {
            return $this->cachedPermissionNames;
        }

        return $this->cachedPermissionNames = Permission::query()
            ->whereHas('roles.users', fn ($q) => $q->where('users.id', $this->id))
            ->pluck('name')
            ->all();
    }

    public function hasPermissionTo(string $permission): bool
    {
        // Super Admin bypass
        if ($this->hasRole('Super Admin')) {
            return true;
        }

        return in_array($permission, $this->permissionNames(), true);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->hasRole('Super Admin')) return true;
        return count(array_intersect($permissions, $this->permissionNames())) > 0;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->hasRole('Super Admin')) return true;
        return count(array_diff($permissions, $this->permissionNames())) === 0;
    }

    public function hasRole(string|array $roles): bool
    {
        $names = (array) $roles;
        return $this->roles()->whereIn('name', $names)->exists();
    }

    public function assignRole(string|array $roles): void
    {
        $ids = Role::whereIn('name', (array) $roles)->pluck('id');
        $this->roles()->syncWithoutDetaching($ids);
        $this->forgetPermissionCache();
    }

    public function syncRoles(array $roleNames): void
    {
        $ids = Role::whereIn('name', $roleNames)->pluck('id');
        $this->roles()->sync($ids);
        $this->forgetPermissionCache();
    }

    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name')->all();
    }

    public function forgetPermissionCache(): void
    {
        $this->cachedPermissionNames = null;
    }
}