<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreZoneRequest;
use App\Http\Requests\Admin\UpdateZoneRequest;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ZoneController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can('view zones'),
            403
        );

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'district' => [
                'nullable',
                'in:Ratnapura,Kegalle',
            ],
            'status' => [
                'nullable',
                'in:active,inactive',
            ],
        ]);

        $zones = Zone::query()
            ->withCount([
                'divisions',
                'schools',
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
                                    'code',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $filters['district'] ?? null,
                fn ($query, string $district) =>
                    $query->where('district', $district)
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
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Zones/Index', [
            'zones' => $zones,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->can('create zones'),
            403
        );

        return Inertia::render('Admin/Zones/Create');
    }

    public function store(
        StoreZoneRequest $request
    ): RedirectResponse {
        Zone::create($request->validated());

        return redirect()
            ->route('admin.zones.index')
            ->with(
                'success',
                'Education zone created successfully.'
            );
    }

    public function show(
        Request $request,
        Zone $zone
    ): Response {
        abort_unless(
            $request->user()->can('view zones'),
            403
        );

        $zone->load([
            'divisions' => fn ($query) =>
                $query
                    ->withCount('schools')
                    ->orderBy('sort_order')
                    ->orderBy('name'),
        ]);

        $zone->loadCount([
            'divisions',
            'schools',
        ]);

        return Inertia::render('Admin/Zones/Show', [
            'zone' => $zone,
        ]);
    }

    public function edit(
        Request $request,
        Zone $zone
    ): Response {
        abort_unless(
            $request->user()->can('edit zones'),
            403
        );

        return Inertia::render('Admin/Zones/Edit', [
            'zone' => $zone,
        ]);
    }

    public function update(
        UpdateZoneRequest $request,
        Zone $zone
    ): RedirectResponse {
        $zone->update($request->validated());

        return redirect()
            ->route('admin.zones.index')
            ->with(
                'success',
                'Education zone updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        Zone $zone
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('delete zones'),
            403
        );

        if ($zone->divisions()->exists()) {
            return back()->with(
                'error',
                'This zone cannot be deleted because it contains divisions.'
            );
        }

        $zone->delete();

        return redirect()
            ->route('admin.zones.index')
            ->with(
                'success',
                'Education zone deleted successfully.'
            );
    }
}
