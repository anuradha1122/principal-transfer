<?php

namespace App\Http\Controllers\Provincial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provincial\ApproveTransferApplicationRequest;
use App\Http\Requests\Provincial\RejectTransferApplicationRequest;
use App\Http\Requests\Provincial\ReturnTransferApplicationRequest;
use App\Models\TransferApplication;
use App\Models\Zone;
use App\Services\ProvincialTransferReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferApplicationController extends Controller
{
    public function __construct(
        private readonly ProvincialTransferReviewService $reviewService
    ) {
    }

    public function index(
        Request $request
    ): Response {
        $this->ensureAccess($request);

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

            'zone_id' => [
                'nullable',
                'integer',
                'exists:zones,id',
            ],

            'transfer_cycle_id' => [
                'nullable',
                'integer',
                'exists:transfer_cycles,id',
            ],
        ]);

        $applications =
            TransferApplication::query()
                ->with([
                    'originZone:id,name,code',
                    'currentSchool:id,name,census_number',
                    'transferCycle:id,name,code,transfer_year',
                    'zonalReview.reviewer:id,name',
                    'provincialReview.reviewer:id,name',
                ])
                ->whereIn(
                    'status',
                    [
                        TransferApplication::STATUS_ZONAL_APPROVED,
                        TransferApplication::STATUS_PROVINCIAL_REVIEW,
                        TransferApplication::STATUS_PROVINCIAL_APPROVED,
                        TransferApplication::STATUS_PROVINCIAL_REJECTED,
                        TransferApplication::STATUS_RETURNED_TO_ZONE,
                    ]
                )
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
                                    )
                                    ->orWhereHas(
                                        'currentSchool',
                                        fn ($schoolQuery) =>
                                            $schoolQuery->where(
                                                'name',
                                                'like',
                                                "%{$search}%"
                                            )
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $filters['status'] ?? null,
                    fn ($query, string $status) =>
                        $query->where(
                            'status',
                            $status
                        )
                )
                ->when(
                    $filters['zone_id'] ?? null,
                    fn ($query, int $zoneId) =>
                        $query->where(
                            'origin_zone_id',
                            $zoneId
                        )
                )
                ->when(
                    $filters['transfer_cycle_id']
                        ?? null,
                    fn ($query, int $cycleId) =>
                        $query->where(
                            'transfer_cycle_id',
                            $cycleId
                        )
                )
                ->latest('submitted_at')
                ->paginate(15)
                ->withQueryString();

        return Inertia::render(
            'Provincial/TransferApplications/Index',
            [
                'applications' =>
                    $applications,

                'filters' =>
                    $filters,

                'statuses' => [
                    TransferApplication::STATUS_ZONAL_APPROVED,
                    TransferApplication::STATUS_PROVINCIAL_REVIEW,
                    TransferApplication::STATUS_PROVINCIAL_APPROVED,
                    TransferApplication::STATUS_PROVINCIAL_REJECTED,
                    TransferApplication::STATUS_RETURNED_TO_ZONE,
                ],

                'zones' =>
                    Zone::query()
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name',
                            'code',
                        ]),
            ]
        );
    }

    public function show(
        Request $request,
        TransferApplication $transferApplication
    ): Response {
        $this->ensureAccess($request);

        abort_unless(
            in_array(
                $transferApplication->status,
                [
                    TransferApplication::STATUS_ZONAL_APPROVED,
                    TransferApplication::STATUS_PROVINCIAL_REVIEW,
                    TransferApplication::STATUS_PROVINCIAL_APPROVED,
                    TransferApplication::STATUS_PROVINCIAL_REJECTED,
                    TransferApplication::STATUS_RETURNED_TO_ZONE,
                ],
                true
            ),
            403
        );

        $transferApplication->load([
            'principalProfile.user:id,name,email',

            'currentSchool:id,division_id,name,census_number',

            'currentSchool.division:id,zone_id,name',

            'currentSchool.division.zone:id,name,code,district',

            'originZone:id,name,code,district',

            'transferCycle',

            'preferences.school:id,division_id,name,census_number',

            'preferences.school.division:id,zone_id,name',

            'preferences.school.division.zone:id,name,code',

            'zonalReview.reviewer:id,name,email',

            'provincialReview.reviewer:id,name,email',

            'actions.actor:id,name',
        ]);

        return Inertia::render(
            'Provincial/TransferApplications/Show',
            [
                'application' =>
                    $transferApplication,

                'can' => [
                    'start_review' =>
                        $transferApplication
                            ->status
                        === TransferApplication::STATUS_ZONAL_APPROVED,

                    'decide' =>
                        $transferApplication
                            ->status
                        === TransferApplication::STATUS_PROVINCIAL_REVIEW,
                ],
            ]
        );
    }

    public function startReview(
        Request $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->ensureAccess($request);

        abort_unless(
            $request->user()->can(
                'review provincial transfer applications'
            ),
            403
        );

        $this->reviewService->startReview(
            $transferApplication,
            $request->user()
        );

        return back()->with(
            'success',
            'Provincial review started successfully.'
        );
    }

    public function approve(
        ApproveTransferApplicationRequest $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->reviewService->approve(
            $transferApplication,
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route(
                'provincial.transfer-applications.show',
                $transferApplication
            )
            ->with(
                'success',
                'Application approved at Provincial level.'
            );
    }

    public function reject(
        RejectTransferApplicationRequest $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->reviewService->reject(
            $transferApplication,
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route(
                'provincial.transfer-applications.show',
                $transferApplication
            )
            ->with(
                'success',
                'Application rejected at Provincial level.'
            );
    }

    public function returnToZone(
        ReturnTransferApplicationRequest $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->reviewService->returnToZone(
            $transferApplication,
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route(
                'provincial.transfer-applications.show',
                $transferApplication
            )
            ->with(
                'success',
                'Application returned to the Zone.'
            );
    }

    public function downloadPdf(
        Request $request,
        TransferApplication $transferApplication
    ): BinaryFileResponse {
        $this->ensureAccess($request);

        abort_unless(
            $request->user()->can(
                'download provincial transfer application pdfs'
            ),
            403
        );

        abort_unless(
            $transferApplication->pdf_path,
            404
        );

        return response()->download(
            storage_path(
                'app/'.$transferApplication->pdf_path
            ),
            $transferApplication->pdf_filename
                ?? "transfer-application-{$transferApplication->id}.pdf"
        );
    }

    private function ensureAccess(
        Request $request
    ): void {
        abort_unless(
            $request->user()->hasAnyRole([
                'Provincial Director',
                'Super Admin',
            ]),
            403
        );
    }
}
