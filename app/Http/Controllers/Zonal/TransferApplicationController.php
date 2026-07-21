<?php

namespace App\Http\Controllers\Zonal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Zonal\ApproveZonalReviewRequest;
use App\Http\Requests\Zonal\RejectZonalReviewRequest;
use App\Http\Requests\Zonal\StartZonalReviewRequest;
use App\Models\School;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Services\TransferApplicationPdfService;
use App\Services\ZonalTransferReviewService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferApplicationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ZonalTransferReviewService $reviewService,
        private readonly TransferApplicationPdfService $pdfService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', TransferApplication::class);

        $user = $request->user();

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'transfer_cycle_id' => [
                'nullable',
                'integer',
                'exists:transfer_cycles,id',
            ],
            'status' => ['nullable', 'string', 'max:50'],
            'school_id' => [
                'nullable',
                'integer',
                'exists:schools,id',
            ],
            'submitted_from' => ['nullable', 'date'],
            'submitted_to' => [
                'nullable',
                'date',
                'after_or_equal:submitted_from',
            ],
        ]);

        $query = TransferApplication::query()
            ->with([
                'transferCycle:id,name,code,transfer_year',
                'principalProfile:id,user_id,full_name,nic,employee_number',
                'principalProfile.user:id,name,email',
                'currentSchool:id,name,census_number,division_id',
                'currentSchool.division:id,name,zone_id',
                'originZone:id,name',
                'zonalReview:id,transfer_application_id,reviewer_id,recommendation,decision,reviewed_at',
                'zonalReview.reviewer:id,name',
            ])
            ->when(
                ! $user->hasRole('Super Admin'),
                fn (Builder $builder) => $builder->where(
                    'origin_zone_id',
                    $user->assigned_zone_id
                )
            )
            ->when(
                $validated['search'] ?? null,
                function (Builder $builder, string $search): void {
                    $builder->where(function (Builder $subQuery) use (
                        $search
                    ): void {
                        $subQuery
                            ->where(
                                'application_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'principal_name_snapshot',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'nic_snapshot',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhereHas(
                                'principalProfile',
                                function (Builder $profileQuery) use (
                                    $search
                                ): void {
                                    $profileQuery
                                        ->where(
                                            'full_name',
                                            'like',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'nic',
                                            'like',
                                            "%{$search}%"
                                        );
                                }
                            );
                    });
                }
            )
            ->when(
                $validated['transfer_cycle_id'] ?? null,
                fn (Builder $builder, int|string $cycleId) => $builder->where(
                    'transfer_cycle_id',
                    $cycleId
                )
            )
            ->when(
                $validated['status'] ?? null,
                fn (Builder $builder, string $status) => $builder->where('status', $status)
            )
            ->when(
                $validated['school_id'] ?? null,
                fn (Builder $builder, int|string $schoolId) => $builder->where(
                    'current_school_id',
                    $schoolId
                )
            )
            ->when(
                $validated['submitted_from'] ?? null,
                fn (Builder $builder, string $date) => $builder->whereDate(
                    'submitted_at',
                    '>=',
                    $date
                )
            )
            ->when(
                $validated['submitted_to'] ?? null,
                fn (Builder $builder, string $date) => $builder->whereDate(
                    'submitted_at',
                    '<=',
                    $date
                )
            )
            ->whereIn('status', [
                TransferApplication::STATUS_SUBMITTED,
                TransferApplication::STATUS_ZONAL_REVIEW,
                TransferApplication::STATUS_ZONAL_APPROVED,
                TransferApplication::STATUS_ZONAL_REJECTED,
            ])
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        $summaryBase = TransferApplication::query()
            ->when(
                ! $user->hasRole('Super Admin'),
                fn (Builder $builder) => $builder->where(
                    'origin_zone_id',
                    $user->assigned_zone_id
                )
            );

        $schools = School::query()
            ->select(['id', 'name', 'census_number'])
            ->when(
                ! $user->hasRole('Super Admin'),
                fn (Builder $builder) => $builder->whereHas(
                    'division',
                    fn (Builder $divisionQuery) => $divisionQuery->where(
                        'zone_id',
                        $user->assigned_zone_id
                    )
                )
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render(
            'Zonal/TransferApplications/Index',
            [
                'applications' => $query,
                'filters' => $validated,
                'cycles' => TransferCycle::query()
                    ->select(['id', 'name', 'code', 'transfer_year'])
                    ->latest('transfer_year')
                    ->orderBy('name')
                    ->get(),
                'schools' => $schools,
                'statuses' => [
                    TransferApplication::STATUS_SUBMITTED,
                    TransferApplication::STATUS_ZONAL_REVIEW,
                    TransferApplication::STATUS_ZONAL_APPROVED,
                    TransferApplication::STATUS_ZONAL_REJECTED,
                ],
                'summary' => [
                    'submitted' => (clone $summaryBase)
                        ->where(
                            'status',
                            TransferApplication::STATUS_SUBMITTED
                        )
                        ->count(),
                    'under_review' => (clone $summaryBase)
                        ->where(
                            'status',
                            TransferApplication::STATUS_ZONAL_REVIEW
                        )
                        ->count(),
                    'approved' => (clone $summaryBase)
                        ->where(
                            'status',
                            TransferApplication::STATUS_ZONAL_APPROVED
                        )
                        ->count(),
                    'rejected' => (clone $summaryBase)
                        ->where(
                            'status',
                            TransferApplication::STATUS_ZONAL_REJECTED
                        )
                        ->count(),
                ],
                'zone' => $user->hasRole('Super Admin')
                    ? null
                    : $user->assignedZone?->only(['id', 'name']),
            ]
        );
    }

    public function show(
        Request $request,
        TransferApplication $transferApplication
    ): Response {
        $this->authorize('view', $transferApplication);

        $transferApplication->load([
            'transferCycle',
            'principalProfile.user',
            'currentSchool.division.zone',
            'originZone',
            'preferences.school.division.zone',
            'zonalReview.reviewer',
            'actions' => fn ($query) => $query->with('actor:id,name')
                ->latestFirst(),
        ]);

        return Inertia::render(
            'Zonal/TransferApplications/Show',
            [
                'application' => $transferApplication,
                'abilities' => [
                    'start_review' => $request->user()->can(
                        'startZonalReview',
                        $transferApplication
                    ),
                    'approve' => $request->user()->can(
                        'approveZonalReview',
                        $transferApplication
                    ),
                    'reject' => $request->user()->can(
                        'rejectZonalReview',
                        $transferApplication
                    ),
                    'download_pdf' => $request->user()->can(
                        'downloadPdf',
                        $transferApplication
                    ),
                ],
                'recommendations' => [
                    'Strongly Recommended',
                    'Recommended',
                    'Recommended with Conditions',
                    'Not Recommended',
                ],
            ]
        );
    }

    public function downloadPdf(
        TransferApplication $transferApplication
    ): BinaryFileResponse {
        $this->authorize('downloadPdf', $transferApplication);

        if (
            ! $transferApplication->pdf_path
            || ! Storage::disk('local')->exists(
                $transferApplication->pdf_path
            )
        ) {
            $this->pdfService->generate($transferApplication->fresh());
        }

        abort_unless(
            $transferApplication->fresh()->pdf_path !== null,
            404,
            'The submitted application PDF is unavailable.'
        );

        $freshApplication = $transferApplication->fresh();

        abort_unless(
            Storage::disk('local')->exists(
                $freshApplication->pdf_path
            ),
            404,
            'The submitted application PDF is unavailable.'
        );

        $filename = sprintf(
            '%s.pdf',
            $freshApplication->application_number
                ?? "transfer-application-{$freshApplication->id}"
        );

        return response()->download(
            Storage::disk('local')->path(
                $freshApplication->pdf_path
            ),
            $filename,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    public function startReview(
        StartZonalReviewRequest $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->reviewService->startReview(
            $transferApplication,
            $request->user()
        );

        return back()->with(
            'success',
            'Zonal review started successfully.'
        );
    }

    public function approve(
        ApproveZonalReviewRequest $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->reviewService->approve(
            $transferApplication,
            $request->user(),
            $request->validated()
        );

        return back()->with(
            'success',
            'The application was approved at Zonal level.'
        );
    }

    public function reject(
        RejectZonalReviewRequest $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->reviewService->reject(
            $transferApplication,
            $request->user(),
            $request->validated()
        );

        return back()->with(
            'success',
            'The application was rejected at Zonal level.'
        );
    }
}
