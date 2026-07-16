<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDivisionRequest;
use App\Http\Requests\Admin\UpdateDivisionRequest;
use App\Models\Division;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DivisionController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can('view divisions'),
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
            'status' => [
                'nullable',
                'in:active,inactive',
            ],
        ]);

        $divisions = Division::query()
            ->with('zone:id,name')
            ->withCount('schools')
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
                                    'code',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $filters['zone_id'] ?? null,
                fn ($query, $zoneId) => $query->where('zone_id', $zoneId)
            )
            ->when(
                ($filters['status'] ?? null) === 'active',
                fn ($query) => $query->where('is_active', true)
            )
            ->when(
                ($filters['status'] ?? null) === 'inactive',
                fn ($query) => $query->where('is_active', false)
            )
            ->orderBy('zone_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Divisions/Index', [
            'divisions' => $divisions,
            'zones' => $this->zoneOptions(),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->can('create divisions'),
            403
        );

        return Inertia::render('Admin/Divisions/Create', [
            'zones' => $this->zoneOptions(),
        ]);
    }

    public function store(
        StoreDivisionRequest $request
    ): RedirectResponse {
        Division::create($request->validated());

        return redirect()
            ->route('admin.divisions.index')
            ->with(
                'success',
                'Education division created successfully.'
            );
    }

    public function show(
        Request $request,
        Division $division
    ): Response {
        abort_unless(
            $request->user()->can('view divisions'),
            403
        );

        $division->load([
            'zone:id,name,code,district',
            'schools' => fn ($query) => $query
                ->orderBy('name')
                ->limit(100),
        ]);

        $division->loadCount('schools');

        return Inertia::render('Admin/Divisions/Show', [
            'division' => $division,
        ]);
    }

    public function edit(
        Request $request,
        Division $division
    ): Response {
        abort_unless(
            $request->user()->can('edit divisions'),
            403
        );

        return Inertia::render('Admin/Divisions/Edit', [
            'division' => $division,
            'zones' => $this->zoneOptions(),
        ]);
    }

    public function update(
        UpdateDivisionRequest $request,
        Division $division
    ): RedirectResponse {
        $division->update($request->validated());

        return redirect()
            ->route('admin.divisions.index')
            ->with(
                'success',
                'Education division updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        Division $division
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('delete divisions'),
            403
        );

        if ($division->schools()->exists()) {
            return back()->with(
                'error',
                'This division cannot be deleted because it contains schools.'
            );
        }

        $division->delete();

        return redirect()
            ->route('admin.divisions.index')
            ->with(
                'success',
                'Education division deleted successfully.'
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
                'code',
                'district',
                'is_active',
            ]);
    }
}
