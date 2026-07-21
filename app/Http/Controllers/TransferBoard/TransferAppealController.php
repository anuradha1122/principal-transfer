<?php

namespace App\Http\Controllers\TransferBoard;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferBoard\ApproveTransferAppealRequest;
use App\Http\Requests\TransferBoard\RejectTransferAppealRequest;
use App\Http\Requests\TransferBoard\ReturnTransferAppealRequest;
use App\Models\School;
use App\Models\TransferAppeal;
use App\Models\TransferAppealDocument;
use App\Models\TransferCycle;
use App\Services\TransferAppealService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferAppealController extends Controller
{
    public function __construct(
        private readonly TransferAppealService $appealService
    ) {
    }

    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can('view transfer appeals'),
            403
        );

        $filters = $request->only([
            'search',
            'status',
            'transfer_cycle_id',
        ]);

        $appeals = TransferAppeal::query()
            ->whereNot('status', TransferAppeal::STATUS_DRAFT)
            ->with([
                'principalProfile:id,user_id,full_name,nic',
                'principalProfile.user:id,name,email',
                'transferApplication:id,application_number,transfer_cycle_id,status',
                'transferApplication.transferCycle:id,name,year',
                'reviewer:id,name',
            ])
            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {
                    $query->where(function ($innerQuery) use ($search) {
                        $innerQuery
                            ->where('appeal_number', 'like', '%'.$search.'%')
                            ->orWhereHas(
                                'transferApplication',
                                fn ($applicationQuery) => $applicationQuery
                                    ->where(
                                        'application_number',
                                        'like',
                                        '%'.$search.'%'
                                    )
                            )
                            ->orWhereHas(
                                'principalProfile',
                                fn ($profileQuery) => $profileQuery
                                    ->where(
                                        'full_name',
                                        'like',
                                        '%'.$search.'%'
                                    )
                                    ->orWhere(
                                        'nic',
                                        'like',
                                        '%'.$search.'%'
                                    )
                            );
                    });
                }
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status)
            )
            ->when(
                $filters['transfer_cycle_id'] ?? null,
                fn ($query, $cycleId) => $query->whereHas(
                    'transferApplication',
                    fn ($applicationQuery) => $applicationQuery->where(
                        'transfer_cycle_id',
                        $cycleId
                    )
                )
            )
            ->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'pending' => TransferAppeal::whereIn('status', [
                TransferAppeal::STATUS_SUBMITTED,
                TransferAppeal::STATUS_RESUBMITTED,
            ])->count(),
            'under_review' => TransferAppeal::where(
                'status',
                TransferAppeal::STATUS_UNDER_REVIEW
            )->count(),
            'returned' => TransferAppeal::where(
                'status',
                TransferAppeal::STATUS_RETURNED
            )->count(),
            'approved' => TransferAppeal::where(
                'status',
                TransferAppeal::STATUS_APPROVED
            )->count(),
            'rejected' => TransferAppeal::where(
                'status',
                TransferAppeal::STATUS_REJECTED
            )->count(),
        ];

        return Inertia::render(
            'TransferBoard/TransferAppeals/Index',
            [
                'appeals' => $appeals,
                'filters' => $filters,
                'statuses' => array_values(array_filter(
                    TransferAppeal::statuses(),
                    fn ($status) => $status !== TransferAppeal::STATUS_DRAFT
                )),
                'cycles' => TransferCycle::query()
                    ->orderByDesc('id')
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                    ]),
                'summary' => $summary,
            ]
        );
    }

    public function show(
        Request $request,
        TransferAppeal $transferAppeal
    ): Response {
        abort_unless(
            $request->user()->can('view transfer appeals'),
            403
        );

        abort_if(
            $transferAppeal->status === TransferAppeal::STATUS_DRAFT,
            404
        );

        $transferAppeal->load([
            'principalProfile.user:id,name,email',
            'transferApplication.transferCycle',
            'transferApplication.currentSchool',
            'transferApplication.transferBoardDecision.recommendedSchool',
            'transferApplication.transferDocuments',
            'documents.uploader:id,name',
            'actions.actor:id,name',
            'reviewer:id,name',
            'revisedSchool:id,name,census_number',
        ]);

        return Inertia::render(
            'TransferBoard/TransferAppeals/Show',
            [
                'appeal' => $transferAppeal,
                'schools' => School::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                        'census_number',
                    ]),
            ]
        );
    }

    public function startReview(
        Request $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('review transfer appeals'),
            403
        );

        $this->appealService->startReview(
            $transferAppeal,
            $request->user()
        );

        return back()->with(
            'success',
            'Appeal review started successfully.'
        );
    }

    public function returnForClarification(
        ReturnTransferAppealRequest $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        $this->appealService->returnForClarification(
            $transferAppeal,
            $request->user(),
            $request->validated('clarification_request')
        );

        return back()->with(
            'success',
            'Appeal returned for clarification.'
        );
    }

    public function approve(
        ApproveTransferAppealRequest $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        $this->appealService->approve(
            $transferAppeal,
            $request->user(),
            $request->validated()
        );

        return back()->with(
            'success',
            'Transfer appeal approved successfully.'
        );
    }

    public function reject(
        RejectTransferAppealRequest $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        $this->appealService->reject(
            $transferAppeal,
            $request->user(),
            $request->validated()
        );

        return back()->with(
            'success',
            'Transfer appeal rejected successfully.'
        );
    }

    public function downloadDocument(
        Request $request,
        TransferAppeal $transferAppeal,
        TransferAppealDocument $document
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can('download transfer appeal documents'),
            403
        );

        abort_unless(
            $document->transfer_appeal_id === $transferAppeal->id,
            404
        );

        abort_unless(
            Storage::disk($document->disk)->exists($document->file_path),
            404
        );

        return response()->download(
            Storage::disk($document->disk)->path($document->file_path),
            $document->original_name,
            [
                'Content-Type' => $document->mime_type
                    ?? 'application/octet-stream',
            ]
        );
    }
}
