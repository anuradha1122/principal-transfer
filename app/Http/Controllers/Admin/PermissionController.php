<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()
                ->can('manage roles and permissions'),
            403
        );

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->withCount('roles')
            ->orderBy('name')
            ->get()
            ->map(fn (Permission $permission): array => [
                'id' => $permission->id,
                'name' => $permission->name,
                'roles_count' => $permission->roles_count,
            ]);

        return Inertia::render(
            'Admin/Permissions/Index',
            [
                'permissions' => $permissions,
            ]
        );
    }

    public function store(
        StorePermissionRequest $request
    ): RedirectResponse {
        Permission::create([
            'name' => strtolower(
                trim($request->validated('name'))
            ),
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $superAdmin = Role::findByName(
            'Super Admin',
            'web'
        );

        $superAdmin->syncPermissions(
            Permission::query()
                ->where('guard_name', 'web')
                ->pluck('name')
        );

        return back()->with(
            'success',
            'Permission created successfully.'
        );
    }

    public function destroy(
        Request $request,
        Permission $permission
    ): RedirectResponse {
        abort_unless(
            $request->user()
                ->can('manage roles and permissions'),
            403
        );

        if ($permission->roles()->exists()) {
            return back()->with(
                'error',
                'Remove this permission from all roles before deleting it.'
            );
        }

        $permission->delete();

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        return back()->with(
            'success',
            'Permission deleted successfully.'
        );
    }
}
