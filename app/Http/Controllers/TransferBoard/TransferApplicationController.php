<?php

namespace App\Http\Controllers\TransferBoard;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferBoard\ApproveTransferApplicationRequest;
use App\Http\Requests\TransferBoard\RejectTransferApplicationRequest;
use App\Http\Requests\TransferBoard\WaitlistTransferApplicationRequest;
use App\Models\School;
use App\Models\TransferApplication;
use App\Models\Zone;
use App\Services\TransferApplicationPdfService;
use App\Services\TransferBoardReviewService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferApplicationController extends Controller
{
    public function __construct(
        private readonly TransferBoardReviewService $reviewService
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
        ]);

        $applications =
            TransferApplication::query()
                ->with([
                    'transferCycle:id,name,code,transfer_year',

                    'currentSchool:id,division_id,name,census_number',

                    'originZone:id,name,code',

                    'provincialReview.reviewer:id,name',

                    'transferBoardDecision.reviewer:id,name',

                    'transferBoardDecision.recommendedSchool:id,name,census_number',
                ])
                ->whereIn(
                    'status',
                    [
                        TransferApplication::STATUS_PROVINCIAL_APPROVED,
                        TransferApplication::STATUS_BOARD_REVIEW,
                        TransferApplication::STATUS_APPROVED,
                        TransferApplication::STATUS_REJECTED,
                        TransferApplication::STATUS_WAITLISTED,
                    ]
                )
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
                                        fn (
                                            Builder $schoolQuery
                                        ) =>
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
                                                )
                                    );
                            }
                        );
                    }
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
                ->paginate(20)
                ->withQueryString();

        return Inertia::render(
            'TransferBoard/TransferApplications/Index',
            [
                'applications' =>
                    $applications,

                'filters' =>
                    $filters,

                'statuses' => [
                    TransferApplication::STATUS_PROVINCIAL_APPROVED,
                    TransferApplication::STATUS_BOARD_REVIEW,
                    TransferApplication::STATUS_APPROVED,
                    TransferApplication::STATUS_REJECTED,
                    TransferApplication::STATUS_WAITLISTED,
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
                    TransferApplication::STATUS_PROVINCIAL_APPROVED,
                    TransferApplication::STATUS_BOARD_REVIEW,
                    TransferApplication::STATUS_APPROVED,
                    TransferApplication::STATUS_REJECTED,
                    TransferApplication::STATUS_WAITLISTED,
                ],
                true
            ),
            403
        );

        $transferApplication->load([
            'principalProfile.user:id,name,email',

            'currentAppointment',

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

            'transferBoardDecision.reviewer:id,name,email',

            'transferBoardDecision.recommendedSchool:id,name,census_number',

            'actions.actor:id,name',
        ]);

        return Inertia::render(
            'TransferBoard/TransferApplications/Show',
            [
                'application' =>
                    $transferApplication,

                'schools' =>
                    School::query()
                        ->with([
                            'division:id,zone_id,name',
                            'division.zone:id,name,code',
                        ])
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy('name')
                        ->get([
                            'id',
                            'division_id',
                            'name',
                            'census_number',
                        ]),

                'appointmentTypes' => [
                    'Permanent',
                    'Acting',
                    'Temporary',
                    'Attached',
                ],

                'can' => [
                    'start_review' =>
                        $transferApplication
                            ->canEnterBoardReview(),

                    'decide' =>
                        $transferApplication
                            ->canReceiveBoardDecision(),
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
                'review board transfer applications'
            ),
            403
        );

        $this->reviewService->startReview(
            $transferApplication,
            $request->user()
        );

        return back()->with(
            'success',
            'Transfer Board review started successfully.'
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
                'transfer-board.transfer-applications.show',
                $transferApplication
            )
            ->with(
                'success',
                'The transfer application was approved.'
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
                'transfer-board.transfer-applications.show',
                $transferApplication
            )
            ->with(
                'success',
                'The transfer application was rejected.'
            );
    }

    public function waitlist(
        WaitlistTransferApplicationRequest $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->reviewService->waitlist(
            $transferApplication,
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route(
                'transfer-board.transfer-applications.show',
                $transferApplication
            )
            ->with(
                'success',
                'The application was placed on the waitlist.'
            );
    }

    public function downloadPdf(
        Request $request,
        TransferApplication $transferApplication,
        TransferApplicationPdfService $pdfService
    ): BinaryFileResponse|RedirectResponse {
        $this->ensureAccess($request);

        abort_unless(
            $request->user()->can(
                'download board transfer application pdfs'
            ),
            403
        );

        if (! $transferApplication->submitted_at) {
            return back()->with(
                'warning',
                'This application has not been submitted.'
            );
        }

        $path = $pdfService->ensureExists(
            $transferApplication
        );

        abort_unless(
            Storage::disk('local')->exists(
                $path
            ),
            404
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

    private function ensureAccess(
        Request $request
    ): void {
        abort_unless(
            $request->user()->hasAnyRole([
                'Transfer Board Member',
                'Super Admin',
            ]),
            403
        );
    }
}
