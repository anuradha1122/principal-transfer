<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PrincipalAppointmentController;
use App\Http\Controllers\Admin\PrincipalProfileController;
use App\Http\Controllers\Admin\PrincipalRegistryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\TransferApplicationController as AdminTransferApplicationController;
use App\Http\Controllers\Admin\TransferCycleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Auth\PrincipalRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Principal\AppointmentController as PrincipalSelfAppointmentController;
use App\Http\Controllers\Principal\ProfileController as PrincipalProfileSelfController;
use App\Http\Controllers\Principal\TransferApplicationController as PrincipalTransferApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Zonal\DashboardController as ZonalDashboardController;
use App\Http\Controllers\Zonal\TransferApplicationController as ZonalTransferApplicationController;
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
| These are Breeze account settings routes. They manage login name,
| account email and password-related settings. They are separate from
| the official principal service profile.
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
| Zonal Routes
|--------------------------------------------------------------------------
|
| These routes are available only to Zonal Directors and Super Admins.
| The assigned zone restriction remains enforced inside the policy and
| controller service layer.
|
*/

Route::middleware([
    'auth',
    'verified',
    'role:Zonal Director|Super Admin',
])
    ->prefix('zonal')
    ->name('zonal.')
    ->group(function (): void {
        /*
        |--------------------------------------------------------------------------
        | Zonal Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            ZonalDashboardController::class
        )
            ->middleware(
                'permission:view zonal dashboard'
            )
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Zonal Transfer Applications
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/transfer-applications',
            [
                ZonalTransferApplicationController::class,
                'index',
            ]
        )
            ->middleware(
                'permission:view zonal transfer applications'
            )
            ->name(
                'transfer-applications.index'
            );

        Route::get(
            '/transfer-applications/{transferApplication}/pdf',
            [
                ZonalTransferApplicationController::class,
                'downloadPdf',
            ]
        )
            ->middleware(
                'permission:download zonal transfer application pdfs'
            )
            ->name(
                'transfer-applications.pdf'
            );

        Route::post(
            '/transfer-applications/{transferApplication}/start-review',
            [
                ZonalTransferApplicationController::class,
                'startReview',
            ]
        )
            ->middleware(
                'permission:review zonal transfer applications'
            )
            ->name(
                'transfer-applications.start-review'
            );

        Route::post(
            '/transfer-applications/{transferApplication}/approve',
            [
                ZonalTransferApplicationController::class,
                'approve',
            ]
        )
            ->middleware(
                'permission:approve zonal transfer applications'
            )
            ->name(
                'transfer-applications.approve'
            );

        Route::post(
            '/transfer-applications/{transferApplication}/reject',
            [
                ZonalTransferApplicationController::class,
                'reject',
            ]
        )
            ->middleware(
                'permission:reject zonal transfer applications'
            )
            ->name(
                'transfer-applications.reject'
            );

        /*
         * Keep the general show route after /pdf and action routes.
         */
        Route::get(
            '/transfer-applications/{transferApplication}',
            [
                ZonalTransferApplicationController::class,
                'show',
            ]
        )
            ->middleware(
                'permission:view zonal transfer applications'
            )
            ->name(
                'transfer-applications.show'
            );
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
            )->name(
                'users.reset-password'
            );

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
            )->name(
                'permissions.index'
            );

            Route::post(
                'permissions',
                [
                    PermissionController::class,
                    'store',
                ]
            )->name(
                'permissions.store'
            );

            Route::delete(
                'permissions/{permission}',
                [
                    PermissionController::class,
                    'destroy',
                ]
            )->name(
                'permissions.destroy'
            );

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
            | Admin Principal Appointment History
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

            /*
            |--------------------------------------------------------------------------
            | Transfer Cycles
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'transfer-cycles',
                TransferCycleController::class
            );

            /*
            |--------------------------------------------------------------------------
            | Admin Transfer Applications
            |--------------------------------------------------------------------------
            */

            Route::get(
                'transfer-applications',
                [
                    AdminTransferApplicationController::class,
                    'index',
                ]
            )->name(
                'transfer-applications.index'
            );

            Route::get(
                'transfer-applications/{transferApplication}/pdf',
                [
                    AdminTransferApplicationController::class,
                    'downloadPdf',
                ]
            )
                ->middleware(
                    'can:view transfer applications'
                )
                ->name(
                    'transfer-applications.pdf'
                );

            Route::get(
                'transfer-applications/{transferApplication}',
                [
                    AdminTransferApplicationController::class,
                    'show',
                ]
            )->name(
                'transfer-applications.show'
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
            /*
            |--------------------------------------------------------------------------
            | Principal Dashboard
            |--------------------------------------------------------------------------
            */

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
            )->name(
                'profile.show'
            );

            Route::get(
                '/profile/edit',
                [
                    PrincipalProfileSelfController::class,
                    'edit',
                ]
            )->name(
                'profile.edit'
            );

            Route::put(
                '/profile',
                [
                    PrincipalProfileSelfController::class,
                    'update',
                ]
            )->name(
                'profile.update'
            );

            /*
            |--------------------------------------------------------------------------
            | Principal Self-Service Appointments
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/appointments/create',
                [
                    PrincipalSelfAppointmentController::class,
                    'create',
                ]
            )->name(
                'appointments.create'
            );

            Route::post(
                '/appointments',
                [
                    PrincipalSelfAppointmentController::class,
                    'store',
                ]
            )->name(
                'appointments.store'
            );

            Route::get(
                '/appointments/{principalAppointment}/edit',
                [
                    PrincipalSelfAppointmentController::class,
                    'edit',
                ]
            )->name(
                'appointments.edit'
            );

            Route::put(
                '/appointments/{principalAppointment}',
                [
                    PrincipalSelfAppointmentController::class,
                    'update',
                ]
            )->name(
                'appointments.update'
            );

            Route::delete(
                '/appointments/{principalAppointment}',
                [
                    PrincipalSelfAppointmentController::class,
                    'destroy',
                ]
            )->name(
                'appointments.destroy'
            );

            /*
            |--------------------------------------------------------------------------
            | Principal Transfer Applications
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/transfer-applications',
                [
                    PrincipalTransferApplicationController::class,
                    'index',
                ]
            )->name(
                'transfer-applications.index'
            );

            /*
             * Keep /create before /{transferApplication}.
             */
            Route::get(
                '/transfer-applications/create',
                [
                    PrincipalTransferApplicationController::class,
                    'create',
                ]
            )->name(
                'transfer-applications.create'
            );

            Route::post(
                '/transfer-applications',
                [
                    PrincipalTransferApplicationController::class,
                    'store',
                ]
            )->name(
                'transfer-applications.store'
            );

            Route::get(
                '/transfer-applications/{transferApplication}/edit',
                [
                    PrincipalTransferApplicationController::class,
                    'edit',
                ]
            )->name(
                'transfer-applications.edit'
            );

            Route::put(
                '/transfer-applications/{transferApplication}',
                [
                    PrincipalTransferApplicationController::class,
                    'update',
                ]
            )->name(
                'transfer-applications.update'
            );

            Route::post(
                '/transfer-applications/{transferApplication}/submit',
                [
                    PrincipalTransferApplicationController::class,
                    'submit',
                ]
            )->name(
                'transfer-applications.submit'
            );

            Route::post(
                '/transfer-applications/{transferApplication}/withdraw',
                [
                    PrincipalTransferApplicationController::class,
                    'withdraw',
                ]
            )->name(
                'transfer-applications.withdraw'
            );

            Route::delete(
                '/transfer-applications/{transferApplication}',
                [
                    PrincipalTransferApplicationController::class,
                    'destroy',
                ]
            )->name(
                'transfer-applications.destroy'
            );

            /*
             * PDF route remains before the general show route.
             */

            Route::get(
                '/transfer-applications/{transferApplication}/pdf',
                [
                    PrincipalTransferApplicationController::class,
                    'downloadPdf',
                ]
            )->name(
                'transfer-applications.pdf'
            );

            Route::get(
                '/transfer-applications/{transferApplication}',
                [
                    PrincipalTransferApplicationController::class,
                    'show',
                ]
            )->name(
                'transfer-applications.show'
            );
        });

    /*
    |--------------------------------------------------------------------------
    | Provincial Director Dashboard Placeholder
    |--------------------------------------------------------------------------
    |
    | This remains temporary until the Provincial review module is built.
    |
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
    )->name(
        'provincial.dashboard'
    );

    /*
    |--------------------------------------------------------------------------
    | Transfer Board Dashboard Placeholder
    |--------------------------------------------------------------------------
    |
    | This remains temporary until the Transfer Board module is built.
    |
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
    )->name(
        'transfer-board.dashboard'
    );
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
