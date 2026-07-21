<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Principal\ClarifyTransferAppealRequest;
use App\Http\Requests\Principal\StoreTransferAppealRequest;
use App\Http\Requests\Principal\SubmitTransferAppealRequest;
use App\Http\Requests\Principal\UpdateTransferAppealRequest;
use App\Models\TransferAppeal;
use App\Models\TransferAppealDocument;
use App\Models\TransferApplication;
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
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless(
            $user->can('view own transfer appeals'),
            403
        );

        $principalProfile = $this->principalProfile($request);

        $filters = $request->only([
            'search',
            'status',
        ]);

        $appeals = TransferAppeal::query()
            ->forPrincipal($principalProfile->id)
            ->with([
                'transferApplication:id,application_number,status,transfer_cycle_id',
                'transferApplication.transferCycle:id,name,year',
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
                            );
                    });
                }
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'draft' => TransferAppeal::forPrincipal($principalProfile->id)
                ->where('status', TransferAppeal::STATUS_DRAFT)
                ->count(),
            'submitted' => TransferAppeal::forPrincipal($principalProfile->id)
                ->whereIn('status', [
                    TransferAppeal::STATUS_SUBMITTED,
                    TransferAppeal::STATUS_RESUBMITTED,
                    TransferAppeal::STATUS_UNDER_REVIEW,
                ])
                ->count(),
            'returned' => TransferAppeal::forPrincipal($principalProfile->id)
                ->where('status', TransferAppeal::STATUS_RETURNED)
                ->count(),
            'completed' => TransferAppeal::forPrincipal($principalProfile->id)
                ->whereIn('status', [
                    TransferAppeal::STATUS_APPROVED,
                    TransferAppeal::STATUS_REJECTED,
                    TransferAppeal::STATUS_WITHDRAWN,
                ])
                ->count(),
        ];

        return Inertia::render('Principal/TransferAppeals/Index', [
            'appeals' => $appeals,
            'filters' => $filters,
            'statuses' => TransferAppeal::statuses(),
            'summary' => $summary,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->can('create transfer appeals'),
            403
        );

        $principalProfile = $this->principalProfile($request);

        $applications = TransferApplication::query()
            ->where('principal_profile_id', $principalProfile->id)
            ->whereIn('status', [
                'Approved',
                'Rejected',
                'Waitlisted',
            ])
            ->whereHas(
                'transferDocuments',
                fn ($query) => $query->where('is_published', true)
            )
            ->whereDoesntHave(
                'transferAppeals',
                fn ($query) => $query->whereIn(
                    'status',
                    TransferAppeal::activeStatuses()
                )
            )
            ->with([
                'transferCycle:id,name,year',
                'transferBoardDecision',
            ])
            ->latest('id')
            ->get();

        return Inertia::render('Principal/TransferAppeals/Create', [
            'applications' => $applications,
        ]);
    }

    public function store(
        StoreTransferAppealRequest $request
    ): RedirectResponse {
        $principalProfile = $this->principalProfile($request);

        $application = TransferApplication::query()
            ->where('principal_profile_id', $principalProfile->id)
            ->findOrFail($request->integer('transfer_application_id'));

        $appeal = $this->appealService->createDraft(
            $application,
            $request->user(),
            $request->validated(),
            $request->file('documents', [])
        );

        return redirect()
            ->route(
                'principal.transfer-appeals.show',
                $appeal
            )
            ->with('success', 'Transfer appeal draft created successfully.');
    }

    public function show(
        Request $request,
        TransferAppeal $transferAppeal
    ): Response {
        $this->authorizeOwnership($request, $transferAppeal);

        $transferAppeal->load([
            'transferApplication.transferCycle',
            'transferApplication.transferBoardDecision.recommendedSchool',
            'documents.uploader:id,name',
            'actions.actor:id,name',
            'reviewer:id,name',
            'revisedSchool:id,name,census_number',
        ]);

        return Inertia::render('Principal/TransferAppeals/Show', [
            'appeal' => $transferAppeal,
        ]);
    }

    public function edit(
        Request $request,
        TransferAppeal $transferAppeal
    ): Response {
        $this->authorizeOwnership($request, $transferAppeal);

        abort_unless(
            $request->user()->can('edit draft transfer appeals'),
            403
        );

        abort_unless($transferAppeal->isDraft(), 403);

        $transferAppeal->load([
            'transferApplication.transferCycle',
            'transferApplication.transferBoardDecision.recommendedSchool',
            'documents',
        ]);

        return Inertia::render('Principal/TransferAppeals/Edit', [
            'appeal' => $transferAppeal,
        ]);
    }

    public function update(
        UpdateTransferAppealRequest $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        $this->authorizeOwnership($request, $transferAppeal);

        $this->appealService->updateDraft(
            $transferAppeal,
            $request->user(),
            $request->validated(),
            $request->file('documents', [])
        );

        return redirect()
            ->route(
                'principal.transfer-appeals.show',
                $transferAppeal
            )
            ->with('success', 'Transfer appeal draft updated successfully.');
    }

    public function submit(
        SubmitTransferAppealRequest $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        $this->authorizeOwnership($request, $transferAppeal);

        $this->appealService->submit(
            $transferAppeal,
            $request->user()
        );

        return back()->with(
            'success',
            'Transfer appeal submitted successfully.'
        );
    }

    public function clarify(
        ClarifyTransferAppealRequest $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        $this->authorizeOwnership($request, $transferAppeal);

        $this->appealService->clarifyAndResubmit(
            $transferAppeal,
            $request->user(),
            $request->validated('clarification_response'),
            $request->file('documents', [])
        );

        return back()->with(
            'success',
            'Clarification submitted and appeal resubmitted.'
        );
    }

    public function withdraw(
        Request $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        $this->authorizeOwnership($request, $transferAppeal);

        abort_unless(
            $request->user()->can('withdraw transfer appeals'),
            403
        );

        $validated = $request->validate([
            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $this->appealService->withdraw(
            $transferAppeal,
            $request->user(),
            $validated['remarks'] ?? null
        );

        return back()->with(
            'success',
            'Transfer appeal withdrawn successfully.'
        );
    }

    public function destroy(
        Request $request,
        TransferAppeal $transferAppeal
    ): RedirectResponse {
        $this->authorizeOwnership($request, $transferAppeal);

        abort_unless(
            $request->user()->can('edit draft transfer appeals'),
            403
        );

        $this->appealService->deleteDraft($transferAppeal);

        return redirect()
            ->route('principal.transfer-appeals.index')
            ->with('success', 'Transfer appeal draft deleted.');
    }

    public function downloadDocument(
        Request $request,
        TransferAppeal $transferAppeal,
        TransferAppealDocument $document
    ): BinaryFileResponse {
        $this->authorizeOwnership($request, $transferAppeal);

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

    public function destroyDocument(
        Request $request,
        TransferAppeal $transferAppeal,
        TransferAppealDocument $document
    ): RedirectResponse {
        $this->authorizeOwnership($request, $transferAppeal);

        abort_unless($transferAppeal->isDraft(), 403);
        abort_unless(
            $document->transfer_appeal_id === $transferAppeal->id,
            404
        );

        $this->appealService->deleteDocument($document);

        return back()->with(
            'success',
            'Supporting document removed.'
        );
    }

    private function principalProfile(Request $request)
    {
        $profile = $request->user()
            ->principalProfile()
            ->first();

        abort_unless($profile, 403, 'Principal Profile not found.');

        return $profile;
    }

    private function authorizeOwnership(
        Request $request,
        TransferAppeal $appeal
    ): void {
        $profile = $this->principalProfile($request);

        abort_unless(
            $appeal->principal_profile_id === $profile->id,
            403
        );
    }
}
