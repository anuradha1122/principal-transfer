<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTransferCycleRequest;
use App\Http\Requests\Admin\UpdateTransferCycleRequest;
use App\Models\TransferCycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransferCycleController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can(
                'view transfer cycles'
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
                'max:50',
            ],
            'year' => [
                'nullable',
                'integer',
            ],
        ]);

        $cycles = TransferCycle::query()
            ->withCount('applications')
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
                $filters['status'] ?? null,
                fn ($query, string $status) => $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $filters['year'] ?? null,
                fn ($query, int $year) => $query->where(
                    'transfer_year',
                    $year
                )
            )
            ->orderByDesc('transfer_year')
            ->orderByDesc('application_open_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render(
            'Admin/TransferCycles/Index',
            [
                'cycles' => $cycles,
                'filters' => $filters,
                'statuses' => $this->statuses(),
                'years' => TransferCycle::query()
                    ->distinct()
                    ->orderByDesc('transfer_year')
                    ->pluck('transfer_year'),
            ]
        );
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->can(
                'manage transfer cycles'
            ),
            403
        );

        return Inertia::render(
            'Admin/TransferCycles/Create',
            [
                'options' => $this->options(),
            ]
        );
    }

    public function store(
        StoreTransferCycleRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $cycle = TransferCycle::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
            'published_at' => $validated['status'] === 'Published'
                    ? now()
                    : null,
            'closed_at' => $validated['status'] === 'Closed'
                    ? now()
                    : null,
        ]);

        return redirect()
            ->route(
                'admin.transfer-cycles.show',
                $cycle
            )
            ->with(
                'success',
                'Transfer cycle created successfully.'
            );
    }

    public function show(
        Request $request,
        TransferCycle $transferCycle
    ): Response {
        abort_unless(
            $request->user()->can(
                'view transfer cycles'
            ),
            403
        );

        $transferCycle->loadCount([
            'applications',
            'applications as draft_applications_count' => fn ($query) => $query->where('status', 'Draft'),
            'applications as submitted_applications_count' => fn ($query) => $query->whereNotIn(
                'status',
                ['Draft', 'Withdrawn']
            ),
            'applications as withdrawn_applications_count' => fn ($query) => $query->where(
                'status',
                'Withdrawn'
            ),
        ]);

        return Inertia::render(
            'Admin/TransferCycles/Show',
            [
                'cycle' => $transferCycle,
            ]
        );
    }

    public function edit(
        Request $request,
        TransferCycle $transferCycle
    ): Response {
        abort_unless(
            $request->user()->can(
                'manage transfer cycles'
            ),
            403
        );

        return Inertia::render(
            'Admin/TransferCycles/Edit',
            [
                'cycle' => $transferCycle,
                'options' => $this->options(),
            ]
        );
    }

    public function update(
        UpdateTransferCycleRequest $request,
        TransferCycle $transferCycle
    ): RedirectResponse {
        $validated = $request->validated();

        $publishedAt =
            $transferCycle->published_at;

        $closedAt =
            $transferCycle->closed_at;

        if (
            $validated['status'] === 'Published'
            && ! $publishedAt
        ) {
            $publishedAt = now();
        }

        if (
            $validated['status'] === 'Closed'
            && ! $closedAt
        ) {
            $closedAt = now();
        }

        $transferCycle->update([
            ...$validated,
            'updated_by' => $request->user()->id,
            'published_at' => $publishedAt,
            'closed_at' => $closedAt,
        ]);

        return redirect()
            ->route(
                'admin.transfer-cycles.show',
                $transferCycle
            )
            ->with(
                'success',
                'Transfer cycle updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        TransferCycle $transferCycle
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'manage transfer cycles'
            ),
            403
        );

        if ($transferCycle->applications()->exists()) {
            return back()->with(
                'error',
                'A transfer cycle with applications cannot be deleted.'
            );
        }

        $transferCycle->delete();

        return redirect()
            ->route(
                'admin.transfer-cycles.index'
            )
            ->with(
                'success',
                'Transfer cycle deleted successfully.'
            );
    }

    private function statuses(): array
    {
        return [
            'Draft',
            'Published',
            'Closed',
            'Completed',
            'Cancelled',
        ];
    }

    private function options(): array
    {
        return [
            'statuses' => $this->statuses(),
            'transferTypes' => [
                'Annual',
                'Special',
                'Mutual',
                'Administrative',
            ],
        ];
    }
}
