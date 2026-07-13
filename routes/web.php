<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
})->name('home');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy',
    ])->name('profile.destroy');
});

Route::middleware([
    'auth',
    'verified',
])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::prefix('admin')
        ->name('admin.')
        ->group(function (): void {
            Route::get(
                '/dashboard',
                [AdminDashboardController::class, 'index']
            )
                ->middleware('can:view admin dashboard')
                ->name('dashboard');

            Route::resource('users', UserController::class);

            Route::put(
                'users/{user}/reset-password',
                [UserController::class, 'resetPassword']
            )->name('users.reset-password');

            Route::resource(
                'roles',
                RoleController::class
            )->except('show');

            Route::get(
                'permissions',
                [PermissionController::class, 'index']
            )->name('permissions.index');

            Route::post(
                'permissions',
                [PermissionController::class, 'store']
            )->name('permissions.store');

            Route::delete(
                'permissions/{permission}',
                [PermissionController::class, 'destroy']
            )->name('permissions.destroy');

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
        });

    Route::get('/principal/dashboard', function () {
        abort_unless(
            request()->user()->can('view principal dashboard'),
            403
        );

        return Inertia::render('Principal/Dashboard/Index');
    })->name('principal.dashboard');

    Route::get('/zonal/dashboard', function () {
        abort_unless(
            request()->user()->can('view zonal dashboard'),
            403
        );

        return Inertia::render('Dashboard', [
            'dashboardTitle' => 'Zonal Director Dashboard',
        ]);
    })->name('zonal.dashboard');

    Route::get('/provincial/dashboard', function () {
        abort_unless(
            request()->user()->can('view provincial dashboard'),
            403
        );

        return Inertia::render('Dashboard', [
            'dashboardTitle' => 'Provincial Director Dashboard',
        ]);
    })->name('provincial.dashboard');

    Route::get('/transfer-board/dashboard', function () {
        abort_unless(
            request()->user()->can(
                'view transfer board dashboard'
            ),
            403
        );

        return Inertia::render('Dashboard', [
            'dashboardTitle' => 'Transfer Board Dashboard',
        ]);
    })->name('transfer-board.dashboard');


});

require __DIR__.'/auth.php';
