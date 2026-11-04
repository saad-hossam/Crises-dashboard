<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserService
{
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => $data['status'],
            ]);

            $this->syncRoles($user, $data['roles'] ?? [], auth()->user());

            return $user;
        });
    }

    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $this->guardProtectedUser($user, auth()->user());

            $update = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'],
            ];

            if (! empty($data['password'])) {
                $update['password'] = Hash::make($data['password']);
            }

            $user->update($update);

            if (isset($data['roles'])) {
                $this->syncRoles($user, $data['roles'], auth()->user());
            }

            return $user;
        });
    }

    public function deleteUser(User $user, User $actor): void
    {
        if ($user->id === $actor->id) {
            throw ValidationException::withMessages(['user' => 'You cannot delete your own account.']);
        }

        $this->guardProtectedUser($user, $actor);

        if ($user->hasRole('Super Admin')) {
            $superAdminCount = User::role('Super Admin')->count();
            if ($superAdminCount <= 1) {
                throw ValidationException::withMessages(['user' => 'Cannot delete the last Super Admin.']);
            }
        }

        DB::transaction(function () use ($user) {
            $user->syncRoles([]);
            $user->delete();
        });
    }

    protected function syncRoles(User $user, array $roleNames, ?User $actor): void
    {
        // Non-Super-Admins cannot assign or revoke the Super Admin role
        if ($actor && ! $actor->hasRole('Super Admin')) {
            if (in_array('Super Admin', $roleNames, true) && ! $user->hasRole('Super Admin')) {
                throw ValidationException::withMessages(['roles' => 'Only Super Admins can assign the Super Admin role.']);
            }

            if (! in_array('Super Admin', $roleNames, true) && $user->hasRole('Super Admin')) {
                throw ValidationException::withMessages(['roles' => 'Only Super Admins can remove the Super Admin role.']);
            }
        }

        $user->syncRoles($roleNames);
    }

    protected function guardProtectedUser(User $target, ?User $actor): void
    {
        if ($target->hasRole('Super Admin') && ! $actor?->hasRole('Super Admin')) {
            throw ValidationException::withMessages(['user' => 'You cannot modify a Super Admin account.']);
        }
    }
}