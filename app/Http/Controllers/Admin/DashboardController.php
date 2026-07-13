<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\School;
use App\Models\Zone;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display the administration dashboard.
     */
    public function index(): Response
    {
        abort_unless(
            request()->user()->can('view admin dashboard'),
            403
        );

        return Inertia::render('Admin/Dashboard/Index', [
            'statistics' => [
                'users' => User::query()->count(),
                'roles' => Role::query()->count(),
                'permissions' => Permission::query()->count(),
                'principals' => User::role('Principal')->count(),
                'zones' => Zone::query()->count(),
                'divisions' => Division::query()->count(),
                'schools' => School::query()->count(),
            ],
        ]);
    }
}
