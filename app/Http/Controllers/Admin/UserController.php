<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetUserPasswordRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can('view users'),
            403
        );

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'role' => [
                'nullable',
                'string',
                'max:100',
            ],
            'status' => [
                'nullable',
                'in:active,inactive',
            ],
        ]);

        $users = User::query()
            ->with([
                'roles:id,name',
                'creator:id,name',
            ])
            ->when(
                $filters['search'] ?? null,
                function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $filters['role'] ?? null,
                fn ($query, string $role) =>
                    $query->role($role)
            )
            ->when(
                ($filters['status'] ?? null) === 'active',
                fn ($query) =>
                    $query->where('is_active', true)
            )
            ->when(
                ($filters['status'] ?? null) === 'inactive',
                fn ($query) =>
                    $query->where('is_active', false)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'email_verified_at' =>
                    $user->email_verified_at?->toDateTimeString(),
                'last_login_at' =>
                    $user->last_login_at?->toDateTimeString(),
                'created_at' =>
                    $user->created_at?->toDateTimeString(),
                'roles' => $user->roles
                    ->pluck('name')
                    ->values(),
                'creator' => $user->creator
                    ? [
                        'id' => $user->creator->id,
                        'name' => $user->creator->name,
                    ]
                    : null,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => Role::query()
                ->where('guard_name', 'web')
                ->orderBy('name')
                ->pluck('name'),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->can('create users'),
            403
        );

        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::query()
                ->where('guard_name', 'web')
                ->orderBy('name')
                ->pluck('name'),
        ]);
    }

    public function store(
        StoreUserRequest $request
    ): RedirectResponse {
        DB::transaction(function () use ($request): void {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'is_active' => $validated['is_active'],
                'email_verified_at' =>
                    $validated['email_verified']
                        ? now()
                        : null,
                'created_by' => $request->user()->id,
            ]);

            $user->syncRoles([
                $validated['role'],
            ]);
        });

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User account created successfully.'
            );
    }

    public function show(
        Request $request,
        User $user
    ): Response {
        abort_unless(
            $request->user()->can('view users'),
            403
        );

        $user->load([
            'roles:id,name',
            'permissions:id,name',
            'creator:id,name,email',
        ]);

        return Inertia::render('Admin/Users/Show', [
            'account' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'email_verified_at' =>
                    $user->email_verified_at?->toDateTimeString(),
                'last_login_at' =>
                    $user->last_login_at?->toDateTimeString(),
                'created_at' =>
                    $user->created_at?->toDateTimeString(),
                'roles' => $user->roles
                    ->pluck('name')
                    ->values(),
                'permissions' => $user
                    ->getAllPermissions()
                    ->pluck('name')
                    ->sort()
                    ->values(),
                'creator' => $user->creator
                    ? [
                        'name' => $user->creator->name,
                        'email' => $user->creator->email,
                    ]
                    : null,
            ],
        ]);
    }

    public function edit(
        Request $request,
        User $user
    ): Response {
        abort_unless(
            $request->user()->can('edit users'),
            403
        );

        $user->load('roles:id,name');

        return Inertia::render('Admin/Users/Edit', [
            'account' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name,
                'is_active' => $user->is_active,
                'email_verified' =>
                    $user->email_verified_at !== null,
            ],
            'roles' => Role::query()
                ->where('guard_name', 'web')
                ->orderBy('name')
                ->pluck('name'),
        ]);
    }

    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validated();

        $this->protectSuperAdminChanges(
            $request,
            $user,
            $validated['role'],
            $validated['is_active']
        );

        DB::transaction(function () use (
            $validated,
            $user
        ): void {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => $validated['is_active'],
                'email_verified_at' =>
                    $validated['email_verified']
                        ? ($user->email_verified_at ?? now())
                        : null,
            ]);

            $user->syncRoles([
                $validated['role'],
            ]);
        });

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User account updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        User $user
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('delete users'),
            403
        );

        if ($request->user()->is($user)) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        if (
            $user->hasRole('Super Admin') &&
            $this->superAdminCount() <= 1
        ) {
            return back()->with(
                'error',
                'The last Super Admin account cannot be deleted.'
            );
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User account deleted successfully.'
            );
    }

    public function resetPassword(
        ResetUserPasswordRequest $request,
        User $user
    ): RedirectResponse {
        $user->update([
            'password' => $request->validated('password'),
        ]);

        return back()->with(
            'success',
            'User password reset successfully.'
        );
    }

    private function protectSuperAdminChanges(
        Request $request,
        User $user,
        string $newRole,
        bool $isActive
    ): void {
        if ($request->user()->is($user) && ! $isActive) {
            abort(
                422,
                'You cannot deactivate your own account.'
            );
        }

        if (
            ! $user->hasRole('Super Admin') ||
            $this->superAdminCount() > 1
        ) {
            return;
        }

        if (
            $newRole !== 'Super Admin' ||
            ! $isActive
        ) {
            abort(
                422,
                'The last Super Admin must remain active and retain the Super Admin role.'
            );
        }
    }

    private function superAdminCount(): int
    {
        return User::query()
            ->role('Super Admin')
            ->where('is_active', true)
            ->count();
    }
}
