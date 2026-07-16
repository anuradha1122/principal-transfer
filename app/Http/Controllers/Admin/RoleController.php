<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()
                ->can('manage roles and permissions'),
            403
        );

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->withCount([
                'users',
                'permissions',
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users_count,
                'permissions_count' => $role->permissions_count,
                'is_system' => in_array(
                    $role->name,
                    $this->systemRoles(),
                    true
                ),
            ]);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()
                ->can('manage roles and permissions'),
            403
        );

        return Inertia::render('Admin/Roles/Create', [
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function store(
        StoreRoleRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('admin.roles.index')
            ->with(
                'success',
                'Role created successfully.'
            );
    }

    public function edit(
        Request $request,
        Role $role
    ): Response {
        abort_unless(
            $request->user()
                ->can('manage roles and permissions'),
            403
        );

        $role->load('permissions:id,name');

        return Inertia::render('Admin/Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role
                    ->permissions
                    ->pluck('name')
                    ->values(),
                'is_system' => in_array(
                    $role->name,
                    $this->systemRoles(),
                    true
                ),
            ],
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function update(
        UpdateRoleRequest $request,
        Role $role
    ): RedirectResponse {
        $validated = $request->validated();

        if (
            $role->name === 'Super Admin' &&
            $validated['name'] !== 'Super Admin'
        ) {
            return back()->with(
                'error',
                'The Super Admin role cannot be renamed.'
            );
        }

        $role->update([
            'name' => $validated['name'],
        ]);

        if ($role->name === 'Super Admin') {
            $role->syncPermissions(
                Permission::query()
                    ->where('guard_name', 'web')
                    ->pluck('name')
            );
        } else {
            $role->syncPermissions(
                $validated['permissions'] ?? []
            );
        }

        return redirect()
            ->route('admin.roles.index')
            ->with(
                'success',
                'Role updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        Role $role
    ): RedirectResponse {
        abort_unless(
            $request->user()
                ->can('manage roles and permissions'),
            403
        );

        if (
            in_array(
                $role->name,
                $this->systemRoles(),
                true
            )
        ) {
            return back()->with(
                'error',
                'System roles cannot be deleted.'
            );
        }

        if ($role->users()->exists()) {
            return back()->with(
                'error',
                'This role is assigned to users and cannot be deleted.'
            );
        }

        $role->delete();

        return back()->with(
            'success',
            'Role deleted successfully.'
        );
    }

    private function permissionGroups(): array
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission): string {
                $name = $permission->name;

                return match (true) {
                    str_contains($name, 'dashboard') => 'Dashboards',

                    str_contains($name, 'user') ||
                    str_contains($name, 'role') => 'Users and Access',

                    str_contains($name, 'zone') ||
                    str_contains($name, 'division') ||
                    str_contains($name, 'school') => 'Organization',

                    str_contains($name, 'principal') => 'Principals',

                    str_contains($name, 'transfer') ||
                    str_contains($name, 'board') => 'Transfer Workflow',

                    str_contains($name, 'report') ||
                    str_contains($name, 'export') ||
                    str_contains($name, 'download') => 'Reports and Exports',

                    str_contains($name, 'setting') ||
                    str_contains($name, 'audit') => 'System',

                    default => 'Other',
                };
            })
            ->map(
                fn ($permissions) => $permissions
                    ->pluck('name')
                    ->values()
            )
            ->toArray();
    }

    private function systemRoles(): array
    {
        return [
            'Super Admin',
            'Principal',
            'Zonal Director',
            'Provincial Director',
            'Transfer Board Member',
            'Data Entry Officer',
        ];
    }
}
