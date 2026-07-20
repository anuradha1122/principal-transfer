<?php

namespace App\Providers;

use App\Models\TransferApplication;
use App\Policies\ZonalTransferApplicationPolicy;
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
        Gate::policy(
            TransferApplication::class,
            ZonalTransferApplicationPolicy::class
        );

        Gate::before(function ($user, string $ability): ?bool {
            return $user->hasRole('Super Admin')
                ? true
                : null;
        });
    }
}
