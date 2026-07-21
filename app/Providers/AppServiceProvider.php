<?php

namespace App\Providers;

use App\Models\TransferApplication;
use App\Models\User;
use App\Policies\ZonalTransferApplicationPolicy;
use App\Services\AuditLogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
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
        /*
        |--------------------------------------------------------------------------
        | Policy registration
        |--------------------------------------------------------------------------
        */

        Gate::policy(
            TransferApplication::class,
            ZonalTransferApplicationPolicy::class
        );

        /*
        |--------------------------------------------------------------------------
        | Super Admin authorization override
        |--------------------------------------------------------------------------
        |
        | Super Admin users receive access before individual policies and gates
        | are evaluated.
        |
        */

        Gate::before(
            function (
                User $user,
                string $ability
            ): ?bool {
                return $user->hasRole('Super Admin')
                    ? true
                    : null;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Successful login auditing
        |--------------------------------------------------------------------------
        */

        Event::listen(
            Login::class,
            function (Login $event): void {
                app(AuditLogService::class)
                    ->authentication(
                        'authentication.login',
                        $event->user,
                        [
                            'description' => sprintf(
                                '%s logged in.',
                                $event->user->name
                            ),
                            'metadata' => [
                                'guard' => $event->guard,
                                'remember' => $event->remember,
                            ],
                        ]
                    );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Logout auditing
        |--------------------------------------------------------------------------
        */

        Event::listen(
            Logout::class,
            function (Logout $event): void {
                app(AuditLogService::class)
                    ->authentication(
                        'authentication.logout',
                        $event->user,
                        [
                            'description' => $event->user
                                ? sprintf(
                                    '%s logged out.',
                                    $event->user->name
                                )
                                : 'A user logged out.',
                            'metadata' => [
                                'guard' => $event->guard,
                            ],
                        ]
                    );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Failed login auditing
        |--------------------------------------------------------------------------
        */

        Event::listen(
            Failed::class,
            function (Failed $event): void {
                app(AuditLogService::class)
                    ->security(
                        'authentication.failed',
                        $event->user,
                        [
                            'description' => 'A failed login attempt occurred.',
                            'metadata' => [
                                'email' => $event->credentials['email']
                                    ?? null,
                                'guard' => $event->guard,
                            ],
                            'user' => $event->user,
                        ]
                    );
            }
        );
    }
}
