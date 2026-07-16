<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSchoolRequest;
use App\Http\Requests\Admin\UpdateSchoolRequest;
use App\Models\Division;
use App\Models\School;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can('view schools'),
            403
        );

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'zone_id' => [
                'nullable',
                'integer',
                'exists:zones,id',
            ],
            'division_id' => [
                'nullable',
                'integer',
                'exists:divisions,id',
            ],
            'school_type' => [
                'nullable',
                'in:1AB,1C,Type 2,Type 3,Other',
            ],
            'status' => [
                'nullable',
                'in:active,inactive',
            ],
        ]);

        $schools = School::query()
            ->with([
                'division:id,zone_id,name',
                'division.zone:id,name',
            ])
            ->when(
                $filters['search'] ?? null,
                function ($query, string $search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'census_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'city',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $filters['zone_id'] ?? null,
                function ($query, $zoneId): void {
                    $query->whereHas(
                        'division',
                        fn ($query) => $query->where(
                            'zone_id',
                            $zoneId
                        )
                    );
                }
            )
            ->when(
                $filters['division_id'] ?? null,
                fn ($query, $divisionId) => $query->where(
                    'division_id',
                    $divisionId
                )
            )
            ->when(
                $filters['school_type'] ?? null,
                fn ($query, string $type) => $query->where('school_type', $type)
            )
            ->when(
                ($filters['status'] ?? null) === 'active',
                fn ($query) => $query->where('is_active', true)
            )
            ->when(
                ($filters['status'] ?? null) === 'inactive',
                fn ($query) => $query->where('is_active', false)
            )
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Schools/Index', [
            'schools' => $schools,
            'zones' => $this->zoneOptions(),
            'divisions' => $this->divisionOptions(),
            'filters' => $filters,
            'schoolTypes' => $this->schoolTypes(),
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->can('create schools'),
            403
        );

        return Inertia::render('Admin/Schools/Create', [
            'zones' => $this->zoneOptions(),
            'divisions' => $this->divisionOptions(),
            'schoolTypes' => $this->schoolTypes(),
            'genderTypes' => $this->genderTypes(),
            'schoolLevels' => $this->schoolLevels(),
            'mediumOptions' => $this->mediumOptions(),
        ]);
    }

    public function store(
        StoreSchoolRequest $request
    ): RedirectResponse {
        School::create($request->validated());

        return redirect()
            ->route('admin.schools.index')
            ->with(
                'success',
                'School created successfully.'
            );
    }

    public function show(
        Request $request,
        School $school
    ): Response {
        abort_unless(
            $request->user()->can('view schools'),
            403
        );

        $school->load([
            'division:id,zone_id,name,code',
            'division.zone:id,name,code,district',
        ]);

        return Inertia::render('Admin/Schools/Show', [
            'school' => $school,
        ]);
    }

    public function edit(
        Request $request,
        School $school
    ): Response {
        abort_unless(
            $request->user()->can('edit schools'),
            403
        );

        $school->load('division:id,zone_id');

        return Inertia::render('Admin/Schools/Edit', [
            'school' => $school,
            'zones' => $this->zoneOptions(),
            'divisions' => $this->divisionOptions(),
            'schoolTypes' => $this->schoolTypes(),
            'genderTypes' => $this->genderTypes(),
            'schoolLevels' => $this->schoolLevels(),
            'mediumOptions' => $this->mediumOptions(),
        ]);
    }

    public function update(
        UpdateSchoolRequest $request,
        School $school
    ): RedirectResponse {
        $school->update($request->validated());

        return redirect()
            ->route('admin.schools.index')
            ->with(
                'success',
                'School updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        School $school
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('delete schools'),
            403
        );

        /*
         * Principal and transfer relationships will be checked here
         * after those modules are introduced.
         */

        $school->delete();

        return redirect()
            ->route('admin.schools.index')
            ->with(
                'success',
                'School deleted successfully.'
            );
    }

    private function zoneOptions()
    {
        return Zone::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'district',
                'is_active',
            ]);
    }

    private function divisionOptions()
    {
        return Division::query()
            ->with('zone:id,name')
            ->orderBy('zone_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'zone_id',
                'name',
                'code',
                'is_active',
            ]);
    }

    private function schoolTypes(): array
    {
        return [
            '1AB',
            '1C',
            'Type 2',
            'Type 3',
            'Other',
        ];
    }

    private function genderTypes(): array
    {
        return [
            'Mixed',
            'Boys',
            'Girls',
        ];
    }

    private function schoolLevels(): array
    {
        return [
            'Primary',
            'Secondary',
            'Primary and Secondary',
        ];
    }

    private function mediumOptions(): array
    {
        return [
            'Sinhala',
            'Tamil',
            'English',
        ];
    }
}
