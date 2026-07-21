<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\Zone;
use App\Services\TransferReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly TransferReportService $transferReportService
    ) {}

    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can('view reports'),
            403
        );

        $filters = [
            'transfer_cycle_id' => $request->input(
                'transfer_cycle_id'
            ),
            'zone_id' => $request->input('zone_id'),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $user = $request->user();

        $zones = Zone::query()
            ->when(
                $user->hasRole('Zonal Director'),
                fn ($query) => $query->where(
                    'id',
                    $user->assigned_zone_id
                )
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return Inertia::render(
            'Admin/Reports/Index',
            [
                'report' => $this->transferReportService
                    ->dashboard(
                        $user,
                        $filters
                    ),

                'filters' => $filters,

                'transferCycles' => TransferCycle::query()
                    ->orderByDesc('id')
                    ->get([
                        'id',
                        'name',
                    ]),

                'zones' => $zones,

                'statuses' => TransferApplication::statusOptions(),

                'scope' => [
                    'is_zonal' => $user->hasRole(
                        'Zonal Director'
                    ),
                    'assigned_zone_id' => $user->assigned_zone_id,
                ],
            ]
        );
    }
}
