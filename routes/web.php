<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PrincipalAppointmentController;
use App\Http\Controllers\Admin\PrincipalProfileController;
use App\Http\Controllers\Admin\PrincipalRegistryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Auth\PrincipalRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Principal\ProfileController as PrincipalProfileSelfController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| NIC-Controlled Principal Registration
|--------------------------------------------------------------------------
|
| Normal Breeze registration remains disabled. Principals may register
| only after successfully verifying an eligible NIC.
|
*/

Route::middleware('guest')->group(function (): void {
    Route::get(
        '/principal-registration',
        [
            PrincipalRegistrationController::class,
            'verifyPage',
        ]
    )->name(
        'principal-registration.verify-page'
    );

    Route::post(
        '/principal-registration/verify-nic',
        [
            PrincipalRegistrationController::class,
            'verify',
        ]
    )->name(
        'principal-registration.verify'
    );

    Route::get(
        '/principal-registration/create',
        [
            PrincipalRegistrationController::class,
            'create',
        ]
    )->name(
        'principal-registration.create'
    );

    Route::post(
        '/principal-registration',
        [
            PrincipalRegistrationController::class,
            'store',
        ]
    )->name(
        'principal-registration.store'
    );
});

/*
|--------------------------------------------------------------------------
| Account Profile Routes
|--------------------------------------------------------------------------
|
| These are the standard Breeze account settings routes. They manage
| account information such as login name, email and password-related
| profile settings. They are separate from the official principal profile.
|
*/

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/profile',
        [
            ProfileController::class,
            'edit',
        ]
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [
            ProfileController::class,
            'update',
        ]
    )->name('profile.update');

    Route::delete(
        '/profile',
        [
            ProfileController::class,
            'destroy',
        ]
    )->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Authenticated and Verified Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Role-Based Dashboard Redirect
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        DashboardController::class
    )->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->name('admin.')
        ->group(function (): void {
            /*
            |--------------------------------------------------------------------------
            | Admin Dashboard
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/dashboard',
                [
                    AdminDashboardController::class,
                    'index',
                ]
            )
                ->middleware(
                    'can:view admin dashboard'
                )
                ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | User Management
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'users',
                UserController::class
            );

            Route::put(
                'users/{user}/reset-password',
                [
                    UserController::class,
                    'resetPassword',
                ]
            )->name('users.reset-password');

            /*
            |--------------------------------------------------------------------------
            | Roles and Permissions
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'roles',
                RoleController::class
            )->except('show');

            Route::get(
                'permissions',
                [
                    PermissionController::class,
                    'index',
                ]
            )->name('permissions.index');

            Route::post(
                'permissions',
                [
                    PermissionController::class,
                    'store',
                ]
            )->name('permissions.store');

            Route::delete(
                'permissions/{permission}',
                [
                    PermissionController::class,
                    'destroy',
                ]
            )->name('permissions.destroy');

            /*
            |--------------------------------------------------------------------------
            | Organization Structure
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'zones',
                ZoneController::class
            );

            Route::resource(
                'divisions',
                DivisionController::class
            );

            Route::resource(
                'schools',
                SchoolController::class
            );

            /*
            |--------------------------------------------------------------------------
            | Principal Registry
            |--------------------------------------------------------------------------
            |
            | Import and template routes must appear before the resource route.
            | Otherwise "import" may be interpreted as a registry route parameter.
            |
            */

            Route::get(
                'principal-registry/import',
                [
                    PrincipalRegistryController::class,
                    'importPage',
                ]
            )->name(
                'principal-registry.import-page'
            );

            Route::post(
                'principal-registry/import',
                [
                    PrincipalRegistryController::class,
                    'import',
                ]
            )->name(
                'principal-registry.import'
            );

            Route::get(
                'principal-registry/template',
                [
                    PrincipalRegistryController::class,
                    'template',
                ]
            )->name(
                'principal-registry.template'
            );

            Route::resource(
                'principal-registry',
                PrincipalRegistryController::class
            )->parameters([
                'principal-registry' =>
                    'principal_registry',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Principal Profiles
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'principal-profiles',
                PrincipalProfileController::class
            )->parameters([
                'principal-profiles' =>
                    'principal_profile',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Principal Appointment History
            |--------------------------------------------------------------------------
            */

            Route::get(
                'principal-profiles/{principalProfile}/appointments/create',
                [
                    PrincipalAppointmentController::class,
                    'create',
                ]
            )->name(
                'principal-profiles.appointments.create'
            );

            Route::post(
                'principal-profiles/{principalProfile}/appointments',
                [
                    PrincipalAppointmentController::class,
                    'store',
                ]
            )->name(
                'principal-profiles.appointments.store'
            );

            Route::get(
                'principal-appointments/{principalAppointment}/edit',
                [
                    PrincipalAppointmentController::class,
                    'edit',
                ]
            )->name(
                'principal-appointments.edit'
            );

            Route::put(
                'principal-appointments/{principalAppointment}',
                [
                    PrincipalAppointmentController::class,
                    'update',
                ]
            )->name(
                'principal-appointments.update'
            );

            Route::delete(
                'principal-appointments/{principalAppointment}',
                [
                    PrincipalAppointmentController::class,
                    'destroy',
                ]
            )->name(
                'principal-appointments.destroy'
            );
        });

    /*
    |--------------------------------------------------------------------------
    | Principal Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('principal')
        ->name('principal.')
        ->middleware('role:Principal')
        ->group(function (): void {
            Route::get(
                '/dashboard',
                function () {
                    abort_unless(
                        request()
                            ->user()
                            ->can(
                                'view principal dashboard'
                            ),
                        403
                    );

                    return Inertia::render(
                        'Principal/Dashboard/Index'
                    );
                }
            )->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Principal Self-Service Profile
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/profile',
                [
                    PrincipalProfileSelfController::class,
                    'show',
                ]
            )->name('profile.show');

            Route::get(
                '/profile/edit',
                [
                    PrincipalProfileSelfController::class,
                    'edit',
                ]
            )->name('profile.edit');

            Route::put(
                '/profile',
                [
                    PrincipalProfileSelfController::class,
                    'update',
                ]
            )->name('profile.update');
        });

    /*
    |--------------------------------------------------------------------------
    | Zonal Director Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/zonal/dashboard',
        function () {
            abort_unless(
                request()
                    ->user()
                    ->can(
                        'view zonal dashboard'
                    ),
                403
            );

            return Inertia::render(
                'Dashboard',
                [
                    'dashboardTitle' =>
                        'Zonal Director Dashboard',
                ]
            );
        }
    )->name('zonal.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Provincial Director Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/provincial/dashboard',
        function () {
            abort_unless(
                request()
                    ->user()
                    ->can(
                        'view provincial dashboard'
                    ),
                403
            );

            return Inertia::render(
                'Dashboard',
                [
                    'dashboardTitle' =>
                        'Provincial Director Dashboard',
                ]
            );
        }
    )->name('provincial.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Transfer Board Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/transfer-board/dashboard',
        function () {
            abort_unless(
                request()
                    ->user()
                    ->can(
                        'view transfer board dashboard'
                    ),
                403
            );

            return Inertia::render(
                'Dashboard',
                [
                    'dashboardTitle' =>
                        'Transfer Board Dashboard',
                ]
            );
        }
    )->name('transfer-board.dashboard');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
