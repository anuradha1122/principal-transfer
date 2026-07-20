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
use App\Http\Controllers\Admin\TransferDocumentController as AdminTransferDocumentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Auth\PrincipalRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Principal\AppointmentController as PrincipalSelfAppointmentController;
use App\Http\Controllers\Principal\ProfileController as PrincipalProfileSelfController;
use App\Http\Controllers\Principal\TransferApplicationController as PrincipalTransferApplicationController;
use App\Http\Controllers\Principal\TransferDocumentController as PrincipalTransferDocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Provincial\DashboardController as ProvincialDashboardController;
use App\Http\Controllers\Provincial\TransferApplicationController as ProvincialTransferApplicationController;
use App\Http\Controllers\PublicTransferResultController;
use App\Http\Controllers\TransferBoard\DashboardController as TransferBoardDashboardController;
use App\Http\Controllers\TransferBoard\TransferApplicationController as TransferBoardTransferApplicationController;
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
| Public Transfer Results
|--------------------------------------------------------------------------
|
| Only published result records are exposed by the controller.
|
*/

Route::get(
    '/transfer-results',
    [
        PublicTransferResultController::class,
        'index',
    ]
)->name(
    'transfer-results.index'
);

Route::get(
    '/transfer-results/{transferDocument}',
    [
        PublicTransferResultController::class,
        'show',
    ]
)->name(
    'transfer-results.show'
);

/*
|--------------------------------------------------------------------------
| NIC-Controlled Principal Registration
|--------------------------------------------------------------------------
*/

Route::middleware('guest')
    ->group(function (): void {
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
*/

Route::middleware('auth')
    ->group(function (): void {
        Route::get(
            '/profile',
            [
                ProfileController::class,
                'edit',
            ]
        )->name(
            'profile.edit'
        );

        Route::patch(
            '/profile',
            [
                ProfileController::class,
                'update',
            ]
        )->name(
            'profile.update'
        );

        Route::delete(
            '/profile',
            [
                ProfileController::class,
                'destroy',
            ]
        )->name(
            'profile.destroy'
        );
    });

/*
|--------------------------------------------------------------------------
| Zonal Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:Zonal Director|Super Admin',
])
    ->prefix('zonal')
    ->name('zonal.')
    ->group(function (): void {
        Route::get(
            '/dashboard',
            ZonalDashboardController::class
        )
            ->middleware(
                'permission:view zonal dashboard'
            )
            ->name(
                'dashboard'
            );

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
         * The general show route remains after PDF and action routes.
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
])
    ->group(function (): void {
        /*
        |--------------------------------------------------------------------------
        | Role-Based Dashboard Redirect
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            DashboardController::class
        )->name(
            'dashboard'
        );

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
                    ->name(
                        'dashboard'
                    );

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
                )->except(
                    'show'
                );

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
                )
                    ->middleware(
                        'permission:view transfer applications'
                    )
                    ->name(
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
                        'permission:download transfer application pdfs'
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
                )
                    ->middleware(
                        'permission:view transfer applications'
                    )
                    ->name(
                        'transfer-applications.show'
                    );

                /*
                |--------------------------------------------------------------------------
                | Transfer Documents and Publication
                |--------------------------------------------------------------------------
                |
                | Static routes remain above /{transferDocument}.
                |
                */

                Route::get(
                    'transfer-documents',
                    [
                        AdminTransferDocumentController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:view transfer documents'
                    )
                    ->name(
                        'transfer-documents.index'
                    );

                Route::get(
                    'transfer-documents/create',
                    [
                        AdminTransferDocumentController::class,
                        'create',
                    ]
                )
                    ->middleware(
                        'permission:generate transfer documents'
                    )
                    ->name(
                        'transfer-documents.create'
                    );

                Route::post(
                    'transfer-documents',
                    [
                        AdminTransferDocumentController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:generate transfer documents'
                    )
                    ->name(
                        'transfer-documents.store'
                    );

                Route::post(
                    'transfer-documents/{transferDocument}/signed-document',
                    [
                        AdminTransferDocumentController::class,
                        'uploadSigned',
                    ]
                )
                    ->middleware(
                        'permission:upload signed transfer documents'
                    )
                    ->name(
                        'transfer-documents.upload-signed'
                    );

                Route::post(
                    'transfer-documents/{transferDocument}/publish',
                    [
                        AdminTransferDocumentController::class,
                        'publish',
                    ]
                )
                    ->middleware(
                        'permission:publish transfer results'
                    )
                    ->name(
                        'transfer-documents.publish'
                    );

                Route::post(
                    'transfer-documents/{transferDocument}/unpublish',
                    [
                        AdminTransferDocumentController::class,
                        'unpublish',
                    ]
                )
                    ->middleware(
                        'permission:unpublish transfer results'
                    )
                    ->name(
                        'transfer-documents.unpublish'
                    );

                Route::post(
                    'transfer-documents/{transferDocument}/regenerate',
                    [
                        AdminTransferDocumentController::class,
                        'regenerate',
                    ]
                )
                    ->middleware(
                        'permission:generate transfer documents'
                    )
                    ->name(
                        'transfer-documents.regenerate'
                    );

                Route::get(
                    'transfer-documents/{transferDocument}/download',
                    [
                        AdminTransferDocumentController::class,
                        'download',
                    ]
                )
                    ->middleware(
                        'permission:download transfer documents'
                    )
                    ->name(
                        'transfer-documents.download'
                    );

                Route::get(
                    'transfer-documents/{transferDocument}',
                    [
                        AdminTransferDocumentController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:view transfer documents'
                    )
                    ->name(
                        'transfer-documents.show'
                    );
            });

        /*
        |--------------------------------------------------------------------------
        | Principal Routes
        |--------------------------------------------------------------------------
        */

        Route::prefix('principal')
            ->name('principal.')
            ->middleware(
                'role:Principal'
            )
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
                )->name(
                    'dashboard'
                );

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
                )
                    ->middleware(
                        'permission:view own transfer applications'
                    )
                    ->name(
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
                )
                    ->middleware(
                        'permission:create transfer applications'
                    )
                    ->name(
                        'transfer-applications.create'
                    );

                Route::post(
                    '/transfer-applications',
                    [
                        PrincipalTransferApplicationController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:create transfer applications'
                    )
                    ->name(
                        'transfer-applications.store'
                    );

                Route::get(
                    '/transfer-applications/{transferApplication}/edit',
                    [
                        PrincipalTransferApplicationController::class,
                        'edit',
                    ]
                )
                    ->middleware(
                        'permission:edit draft transfer applications'
                    )
                    ->name(
                        'transfer-applications.edit'
                    );

                Route::put(
                    '/transfer-applications/{transferApplication}',
                    [
                        PrincipalTransferApplicationController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:edit draft transfer applications'
                    )
                    ->name(
                        'transfer-applications.update'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/submit',
                    [
                        PrincipalTransferApplicationController::class,
                        'submit',
                    ]
                )
                    ->middleware(
                        'permission:submit transfer applications'
                    )
                    ->name(
                        'transfer-applications.submit'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/withdraw',
                    [
                        PrincipalTransferApplicationController::class,
                        'withdraw',
                    ]
                )
                    ->middleware(
                        'permission:withdraw transfer applications'
                    )
                    ->name(
                        'transfer-applications.withdraw'
                    );

                Route::delete(
                    '/transfer-applications/{transferApplication}',
                    [
                        PrincipalTransferApplicationController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:delete draft transfer applications'
                    )
                    ->name(
                        'transfer-applications.destroy'
                    );

                Route::get(
                    '/transfer-applications/{transferApplication}/pdf',
                    [
                        PrincipalTransferApplicationController::class,
                        'downloadPdf',
                    ]
                )
                    ->middleware(
                        'permission:download own transfer application pdfs'
                    )
                    ->name(
                        'transfer-applications.pdf'
                    );

                Route::get(
                    '/transfer-applications/{transferApplication}',
                    [
                        PrincipalTransferApplicationController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:view own transfer applications'
                    )
                    ->name(
                        'transfer-applications.show'
                    );

                /*
                |--------------------------------------------------------------------------
                | Principal Official Transfer Documents
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/transfer-documents',
                    [
                        PrincipalTransferDocumentController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:view own transfer documents'
                    )
                    ->name(
                        'transfer-documents.index'
                    );

                Route::get(
                    '/transfer-documents/{transferDocument}/download',
                    [
                        PrincipalTransferDocumentController::class,
                        'download',
                    ]
                )
                    ->middleware(
                        'permission:download own transfer documents'
                    )
                    ->name(
                        'transfer-documents.download'
                    );

                Route::get(
                    '/transfer-documents/{transferDocument}',
                    [
                        PrincipalTransferDocumentController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:view own transfer documents'
                    )
                    ->name(
                        'transfer-documents.show'
                    );
            });

        /*
        |--------------------------------------------------------------------------
        | Provincial Director Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware(
            'role:Provincial Director|Super Admin'
        )
            ->prefix('provincial')
            ->name('provincial.')
            ->group(function (): void {
                Route::get(
                    '/dashboard',
                    ProvincialDashboardController::class
                )
                    ->middleware(
                        'permission:view provincial dashboard'
                    )
                    ->name(
                        'dashboard'
                    );

                Route::get(
                    '/transfer-applications',
                    [
                        ProvincialTransferApplicationController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:view provincial transfer applications'
                    )
                    ->name(
                        'transfer-applications.index'
                    );

                Route::get(
                    '/transfer-applications/{transferApplication}/pdf',
                    [
                        ProvincialTransferApplicationController::class,
                        'downloadPdf',
                    ]
                )
                    ->middleware(
                        'permission:download provincial transfer application pdfs'
                    )
                    ->name(
                        'transfer-applications.pdf'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/start-review',
                    [
                        ProvincialTransferApplicationController::class,
                        'startReview',
                    ]
                )
                    ->middleware(
                        'permission:review provincial transfer applications'
                    )
                    ->name(
                        'transfer-applications.start-review'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/approve',
                    [
                        ProvincialTransferApplicationController::class,
                        'approve',
                    ]
                )
                    ->middleware(
                        'permission:approve provincial transfer applications'
                    )
                    ->name(
                        'transfer-applications.approve'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/reject',
                    [
                        ProvincialTransferApplicationController::class,
                        'reject',
                    ]
                )
                    ->middleware(
                        'permission:reject provincial transfer applications'
                    )
                    ->name(
                        'transfer-applications.reject'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/return-to-zone',
                    [
                        ProvincialTransferApplicationController::class,
                        'returnToZone',
                    ]
                )
                    ->middleware(
                        'permission:return provincial transfer applications'
                    )
                    ->name(
                        'transfer-applications.return-to-zone'
                    );

                Route::get(
                    '/transfer-applications/{transferApplication}',
                    [
                        ProvincialTransferApplicationController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:view provincial transfer applications'
                    )
                    ->name(
                        'transfer-applications.show'
                    );
            });

        /*
        |--------------------------------------------------------------------------
        | Transfer Board Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware(
            'role:Transfer Board Member|Super Admin'
        )
            ->prefix('transfer-board')
            ->name('transfer-board.')
            ->group(function (): void {
                Route::get(
                    '/dashboard',
                    TransferBoardDashboardController::class
                )
                    ->middleware(
                        'permission:view transfer board dashboard'
                    )
                    ->name(
                        'dashboard'
                    );

                Route::get(
                    '/transfer-applications',
                    [
                        TransferBoardTransferApplicationController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:view board transfer applications'
                    )
                    ->name(
                        'transfer-applications.index'
                    );

                Route::get(
                    '/transfer-applications/{transferApplication}/pdf',
                    [
                        TransferBoardTransferApplicationController::class,
                        'downloadPdf',
                    ]
                )
                    ->middleware(
                        'permission:download board transfer application pdfs'
                    )
                    ->name(
                        'transfer-applications.pdf'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/start-review',
                    [
                        TransferBoardTransferApplicationController::class,
                        'startReview',
                    ]
                )
                    ->middleware(
                        'permission:review board transfer applications'
                    )
                    ->name(
                        'transfer-applications.start-review'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/approve',
                    [
                        TransferBoardTransferApplicationController::class,
                        'approve',
                    ]
                )
                    ->middleware(
                        'permission:record transfer board decisions'
                    )
                    ->name(
                        'transfer-applications.approve'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/reject',
                    [
                        TransferBoardTransferApplicationController::class,
                        'reject',
                    ]
                )
                    ->middleware(
                        'permission:record transfer board decisions'
                    )
                    ->name(
                        'transfer-applications.reject'
                    );

                Route::post(
                    '/transfer-applications/{transferApplication}/waitlist',
                    [
                        TransferBoardTransferApplicationController::class,
                        'waitlist',
                    ]
                )
                    ->middleware(
                        'permission:record transfer board decisions'
                    )
                    ->name(
                        'transfer-applications.waitlist'
                    );

                Route::get(
                    '/transfer-applications/{transferApplication}',
                    [
                        TransferBoardTransferApplicationController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:view board transfer applications'
                    )
                    ->name(
                        'transfer-applications.show'
                    );
            });
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
