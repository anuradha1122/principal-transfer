<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\Zone;
use App\Services\TransferApplicationPdfService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferApplicationController extends Controller
{
    public function index(
        Request $request
    ): Response {
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
                'max:100',
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

                    'principalProfile:id,user_id,full_name,nic',

                    'currentSchool:id,division_id,name,census_number',

                    'currentSchool.division:id,zone_id,name',

                    'currentSchool.division.zone:id,name,code,district',

                    'originZone:id,name,code,district',

                    'zonalReview:id,transfer_application_id,reviewer_id,decision,recommendation,reviewed_at',

                    'zonalReview.reviewer:id,name',

                    'provincialReview:id,transfer_application_id,reviewer_id,decision,recommendation,reviewed_at',

                    'provincialReview.reviewer:id,name',
                ])
                ->when(
                    $filters['search'] ?? null,
                    function (
                        Builder $query,
                        string $search
                    ): void {
                        $query->where(
                            function (
                                Builder $query
                            ) use (
                                $search
                            ): void {
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
                                    )
                                    ->orWhereHas(
                                        'currentSchool',
                                        function (
                                            Builder $schoolQuery
                                        ) use (
                                            $search
                                        ): void {
                                            $schoolQuery
                                                ->where(
                                                    'name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'census_number',
                                                    'like',
                                                    "%{$search}%"
                                                );
                                        }
                                    )
                                    ->orWhereHas(
                                        'originZone',
                                        function (
                                            Builder $zoneQuery
                                        ) use (
                                            $search
                                        ): void {
                                            $zoneQuery
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
                        );
                    }
                )
                ->when(
                    $filters['transfer_cycle_id']
                        ?? null,
                    fn (
                        Builder $query,
                        int $cycleId
                    ): Builder =>
                        $query->where(
                            'transfer_cycle_id',
                            $cycleId
                        )
                )
                ->when(
                    $filters['status'] ?? null,
                    fn (
                        Builder $query,
                        string $status
                    ): Builder =>
                        $query->where(
                            'status',
                            $status
                        )
                )
                ->when(
                    $filters['zone_id'] ?? null,
                    fn (
                        Builder $query,
                        int $zoneId
                    ): Builder =>
                        $query->where(
                            'origin_zone_id',
                            $zoneId
                        )
                )
                ->latest('submitted_at')
                ->latest('id')
                ->paginate(25)
                ->withQueryString();

        return Inertia::render(
            'Admin/TransferApplications/Index',
            [
                'applications' =>
                    $applications,

                'filters' =>
                    $filters,

                'cycles' =>
                    TransferCycle::query()
                        ->orderByDesc(
                            'transfer_year'
                        )
                        ->orderByDesc('id')
                        ->get([
                            'id',
                            'name',
                            'code',
                            'transfer_year',
                            'status',
                        ]),

                'zones' =>
                    Zone::query()
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name',
                            'code',
                            'district',
                        ]),

                'statuses' =>
                    $this->statuses(),
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

            'currentAppointment',

            'currentSchool:id,division_id,name,census_number,school_type,gender_type',

            'currentSchool.division:id,zone_id,name,code',

            'currentSchool.division.zone:id,name,code,district',

            'originZone:id,name,code,district',

            'preferences.school:id,division_id,name,census_number',

            'preferences.school.division:id,zone_id,name,code',

            'preferences.school.division.zone:id,name,code,district',

            'zonalReview.reviewer:id,name,email',

            'provincialReview.reviewer:id,name,email',

            'actions.actor:id,name,email',
        ]);

        return Inertia::render(
            'Admin/TransferApplications/Show',
            [
                'application' =>
                    $transferApplication,

                'workflow' => [
                    'zonal_review' =>
                        $transferApplication
                            ->zonalReview,

                    'provincial_review' =>
                        $transferApplication
                            ->provincialReview,

                    'actions' =>
                        $transferApplication
                            ->actions
                            ->sortByDesc(
                                'acted_at'
                            )
                            ->values(),
                ],
            ]
        );
    }

    public function downloadPdf(
        Request $request,
        TransferApplication $transferApplication,
        TransferApplicationPdfService $pdfService
    ): BinaryFileResponse|RedirectResponse {
        abort_unless(
            $request->user()->can(
                'download transfer application pdfs'
            ),
            403
        );

        if (
            ! $transferApplication
                ->submitted_at
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

        abort_unless(
            Storage::disk('local')
                ->exists($path),
            404,
            'The transfer application PDF could not be found.'
        );

        return response()->download(
            Storage::disk('local')
                ->path($path),
            $pdfService->downloadName(
                $transferApplication
            ),
            [
                'Content-Type' =>
                    'application/pdf',
            ]
        );
    }

    private function statuses(): array
    {
        return TransferApplication::STATUSES;
    }
}
