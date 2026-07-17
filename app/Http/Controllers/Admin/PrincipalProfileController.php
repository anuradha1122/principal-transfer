<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePrincipalProfileRequest;
use App\Http\Requests\Admin\UpdatePrincipalProfileRequest;
use App\Models\PrincipalProfile;
use App\Models\PrincipalRegistry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PrincipalProfileController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can(
                'view principal profiles'
            ),
            403
        );

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'nullable',
                'string',
                'max:100',
            ],
            'service_grade' => [
                'nullable',
                'string',
                'max:100',
            ],
            'zone_id' => [
                'nullable',
                'integer',
                'exists:zones,id',
            ],
            'school_id' => [
                'nullable',
                'integer',
                'exists:schools,id',
            ],
        ]);

        $profiles = PrincipalProfile::query()
            ->with([
                'user:id,name,email,is_active',
                'currentAppointment.school.division.zone',
            ])
            ->when(
                $filters['search'] ?? null,
                function ($query, string $search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where(
                                    'full_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'name_with_initials',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'nic',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'employee_number',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, string $status) =>
                    $query->where(
                        'employment_status',
                        $status
                    )
            )
            ->when(
                $filters['service_grade'] ?? null,
                fn ($query, string $grade) =>
                    $query->where(
                        'service_grade',
                        $grade
                    )
            )
            ->when(
                $filters['school_id'] ?? null,
                fn ($query, $schoolId) =>
                    $query->whereHas(
                        'currentAppointment',
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    )
            )
            ->when(
                $filters['zone_id'] ?? null,
                function ($query, $zoneId): void {
                    $query->whereHas(
                        'currentAppointment.school.division',
                        fn ($query) =>
                            $query->where(
                                'zone_id',
                                $zoneId
                            )
                    );
                }
            )
            ->orderBy('full_name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render(
            'Admin/PrincipalProfiles/Index',
            [
                'profiles' => $profiles,
                'filters' => $filters,
                'statuses' => $this->statuses(),
                'serviceGrades' =>
                    PrincipalProfile::query()
                        ->whereNotNull('service_grade')
                        ->distinct()
                        ->orderBy('service_grade')
                        ->pluck('service_grade'),
                'zones' => \App\Models\Zone::query()
                    ->orderBy('sort_order')
                    ->get(['id', 'name']),
                'schools' => \App\Models\School::query()
                    ->orderBy('name')
                    ->get(['id', 'name']),
            ]
        );
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->can(
                'manage principal profiles'
            ),
            403
        );

        return Inertia::render(
            'Admin/PrincipalProfiles/Create',
            [
                'availableAccounts' =>
                    $this->availableAccounts(),
                'registries' =>
                    $this->availableRegistries(),
                'options' => $this->options(),
            ]
        );
    }

    public function store(
        StorePrincipalProfileRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(
            function () use (
                $validated,
                $request
            ): void {
                PrincipalProfile::create([
                    ...$validated,
                    'created_by' =>
                        $request->user()->id,
                    'updated_by' =>
                        $request->user()->id,
                ]);
            }
        );

        return redirect()
            ->route(
                'admin.principal-profiles.index'
            )
            ->with(
                'success',
                'Principal profile created successfully.'
            );
    }

    public function show(
        Request $request,
        PrincipalProfile $principalProfile
    ): Response {
        abort_unless(
            $request->user()->can(
                'view principal profiles'
            ),
            403
        );

        $principalProfile->load([
            'user:id,name,email,is_active,email_verified_at,last_login_at',
            'registry:id,nic,designation,registration_status',
            'appointments' => fn ($query) =>
                $query
                    ->with(
                        'school.division.zone'
                    )
                    ->orderByDesc('start_date'),
        ]);

        return Inertia::render(
            'Admin/PrincipalProfiles/Show',
            [
                'profile' => $principalProfile,
            ]
        );
    }

    public function edit(
        Request $request,
        PrincipalProfile $principalProfile
    ): Response {
        abort_unless(
            $request->user()->can(
                'manage principal profiles'
            ),
            403
        );

        return Inertia::render(
            'Admin/PrincipalProfiles/Edit',
            [
                'profile' => $principalProfile,
                'options' => $this->options(),
            ]
        );
    }

    public function update(
        UpdatePrincipalProfileRequest $request,
        PrincipalProfile $principalProfile
    ): RedirectResponse {
        $principalProfile->update([
            ...$request->validated(),
            'updated_by' =>
                $request->user()->id,
        ]);

        return redirect()
            ->route(
                'admin.principal-profiles.show',
                $principalProfile
            )
            ->with(
                'success',
                'Principal profile updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        PrincipalProfile $principalProfile
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'manage principal profiles'
            ),
            403
        );

        if (
            $principalProfile
                ->appointments()
                ->exists()
        ) {
            return back()->with(
                'error',
                'A principal profile with appointment history cannot be deleted.'
            );
        }

        $principalProfile->delete();

        return redirect()
            ->route(
                'admin.principal-profiles.index'
            )
            ->with(
                'success',
                'Principal profile deleted successfully.'
            );
    }

    private function availableAccounts()
    {
        return User::query()
            ->role('Principal')
            ->whereDoesntHave('principalProfile')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
            ]);
    }

    private function availableRegistries()
    {
        return PrincipalRegistry::query()
            ->whereNotNull('registered_user_id')
            ->whereDoesntHave('principalProfile')
            ->with('registeredUser:id,name,email')
            ->orderBy('full_name')
            ->get();
    }

    private function options(): array
    {
        return [
            'genders' => [
                'Male',
                'Female',
                'Other',
            ],
            'serviceCategories' => [
                'Sri Lanka Principals Service',
                'Sri Lanka Education Administrative Service',
                'Other',
            ],
            'statuses' => $this->statuses(),
        ];
    }

    private function statuses(): array
    {
        return [
            'Active',
            'Retired',
            'Transferred Out',
            'Suspended',
            'Deceased',
            'Other',
        ];
    }
}
