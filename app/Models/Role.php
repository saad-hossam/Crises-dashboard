<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'is_protected'];

    protected $casts = ['is_protected' => 'boolean'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    public function givePermissionTo(string|array $permissions): void
    {
        $ids = Permission::whereIn('name', (array) $permissions)->pluck('id');
        $this->permissions()->syncWithoutDetaching($ids);
    }

    public function syncPermissions(array $permissionNames): void
    {
        $ids = Permission::whereIn('name', $permissionNames)->pluck('id');
        $this->permissions()->sync($ids);
    }

    public function syncUsers(array $userIds): void
    {
        $this->users()->sync($userIds);
    }
}