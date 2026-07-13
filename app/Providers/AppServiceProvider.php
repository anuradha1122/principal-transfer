<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        Gate::before(
            function (User $user, string $ability): ?bool {
                return $user->hasRole('Super Admin')
                    ? true
                    : null;
            }
        );
    }
}
