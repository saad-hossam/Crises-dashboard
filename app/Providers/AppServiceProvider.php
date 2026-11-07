<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
                Paginator::useBootstrapFive();

        /*
         * Route any `@can('users.view')` / `Gate::authorize('users.view')`
         * call through the user's HasPermissions trait.
         *
         * Returning `null` (not `false`) for users who don't have the ability
         * lets Laravel fall through to its normal policy/gate checks.
         * Returning `true` for Super Admin short-circuits everything.
         */
        Gate::before(function ($user, string $ability) {
            return $user->hasPermissionTo($ability) ?: null;
        });
    }
}