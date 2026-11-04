<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'group', 'description'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    public static function knownGroups(): array
    {
        return static::query()
            ->whereNotNull('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->all();
    }
}