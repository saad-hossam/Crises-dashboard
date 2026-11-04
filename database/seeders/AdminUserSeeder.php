<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email    = 'admin@gmail.com';
        $password = '12345678';
        $name     = 'admin';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->command->info("Admin user created: {$email}");
        } else {
            // Refresh only the fields we want to guarantee on re-seed.
            $user->update([
                'name' => $name,
                'password' => Hash::make($password),
                'status' => 'active',
            ]);
            $this->command->info("Admin user refreshed: {$email}");
        }

        if (! Role::where('name', 'Super Admin')->exists()) {
            $this->command->warn(
                "Role 'Super Admin' not found. Run RoleSeeder first, then re-run AdminUserSeeder."
            );
            return;
        }

        if (! $user->hasRole('Super Admin')) {
            $user->assignRole('Super Admin');
            $this->command->info("Super Admin role assigned to {$email}");
        } else {
            $this->command->info("{$email} already has Super Admin role");
        }
    }
}