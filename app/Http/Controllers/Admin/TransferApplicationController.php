<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\TransferApplicationPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferApplicationController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can(
                'view transfer applications'
            ),
            403
        );

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'transfer_cycle_id' => [
                'nullable',
                'integer',
                'exists:transfer_cycles,id',
            ],
            'status' => [
                'nullable',
                'string',
                'max:50',
            ],
            'zone_id' => [
                'nullable',
                'integer',
                'exists:zones,id',
            ],
        ]);

        $applications =
            TransferApplication::query()
                ->with([
                    'transferCycle:id,name,code,transfer_year',
                    'principalProfile:id,full_name,nic',
                    'currentSchool.division.zone',
                ])
                ->when(
                    $filters['search'] ?? null,
                    function (
                        $query,
                        string $search
                    ): void {
                        $query->where(
                            function ($query) use ($search): void {
                                $query
                                    ->where(
                                        'application_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'principal_name',
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
                    $filters['transfer_cycle_id']
                        ?? null,
                    fn ($query, $cycleId) => $query->where(
                        'transfer_cycle_id',
                        $cycleId
                    )
                )
                ->when(
                    $filters['status'] ?? null,
                    fn ($query, $status) => $query->where(
                        'status',
                        $status
                    )
                )
                ->when(
                    $filters['zone_id'] ?? null,
                    function ($query, $zoneId): void {
                        $query->whereHas(
                            'currentSchool.division',
                            fn ($query) => $query->where(
                                'zone_id',
                                $zoneId
                            )
                        );
                    }
                )
                ->latest('submitted_at')
                ->latest('id')
                ->paginate(25)
                ->withQueryString();

        return Inertia::render(
            'Admin/TransferApplications/Index',
            [
                'applications' => $applications,
                'filters' => $filters,
                'cycles' => TransferCycle::query()
                    ->orderByDesc(
                        'transfer_year'
                    )
                    ->get([
                        'id',
                        'name',
                        'code',
                    ]),
                'zones' => Zone::query()
                    ->orderBy('sort_order')
                    ->get([
                        'id',
                        'name',
                    ]),
                'statuses' => $this->statuses(),
            ]
        );
    }

    public function show(
        Request $request,
        TransferApplication $transferApplication
    ): Response {
        abort_unless(
            $request->user()->can(
                'view transfer applications'
            ),
            403
        );

        $transferApplication->load([
            'transferCycle',
            'principalProfile.user:id,name,email',
            'currentSchool.division.zone',
            'preferences.school.division.zone',
        ]);

        return Inertia::render(
            'Admin/TransferApplications/Show',
            [
                'application' => $transferApplication,
            ]
        );
    }

    private function statuses(): array
    {
        return [
            'Draft',
            'Submitted',
            'Zonal Review',
            'Zonal Approved',
            'Zonal Rejected',
            'Provincial Review',
            'Provincial Approved',
            'Provincial Rejected',
            'Board Review',
            'Approved',
            'Rejected',
            'Waitlisted',
            'Withdrawn',
            'Cancelled',
        ];
    }

    public function downloadPdf(
        TransferApplication $transferApplication,
        TransferApplicationPdfService $pdfService
    ): BinaryFileResponse|RedirectResponse {
        if (
            ! $transferApplication->submitted_at
        ) {
            return redirect()
                ->route(
                    'admin.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'This application has not been submitted yet.'
                );
        }

        $path = $pdfService->ensureExists(
            $transferApplication
        );

        return response()->download(
            Storage::disk('local')->path(
                $path
            ),
            $pdfService->downloadName(
                $transferApplication
            ),
            [
                'Content-Type' =>
                    'application/pdf',
            ]
        );
    }
}
