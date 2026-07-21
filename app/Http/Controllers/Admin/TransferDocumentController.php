<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublishTransferDocumentRequest;
use App\Http\Requests\Admin\StoreTransferDocumentRequest;
use App\Http\Requests\Admin\UploadSignedTransferDocumentRequest;
use App\Models\TransferApplication;
use App\Models\TransferDocument;
use App\Services\TransferDocumentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferDocumentController extends Controller
{
    public function __construct(
        private readonly TransferDocumentService $documentService
    ) {}

    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'view transfer documents'
            ),
            403
        );

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'document_type' => [
                'nullable',
                'string',
                'max:100',
            ],

            'publication_status' => [
                'nullable',
                'string',
                'in:published,unpublished',
            ],
        ]);

        $documents =
            TransferDocument::query()
                ->with([
                    'transferApplication:id,application_number,principal_name,nic,status,current_school_id,origin_zone_id',
                    'transferApplication.currentSchool:id,name',
                    'transferApplication.originZone:id,name,code',
                    'issuer:id,name',
                    'publisher:id,name',
                ])
                ->when(
                    $filters['search']
                        ?? null,
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
                                        'document_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhereHas(
                                        'transferApplication',
                                        function (
                                            Builder $applicationQuery
                                        ) use (
                                            $search
                                        ): void {
                                            $applicationQuery
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
                                                );
                                        }
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $filters['document_type']
                        ?? null,
                    fn (
                        Builder $query,
                        string $type
                    ): Builder => $query->where(
                        'document_type',
                        $type
                    )
                )
                ->when(
                    ($filters['publication_status']
                        ?? null)
                    === 'published',
                    fn (
                        Builder $query
                    ): Builder => $query->where(
                        'is_published',
                        true
                    )
                )
                ->when(
                    ($filters['publication_status']
                        ?? null)
                    === 'unpublished',
                    fn (
                        Builder $query
                    ): Builder => $query->where(
                        'is_published',
                        false
                    )
                )
                ->latest('issued_date')
                ->latest('id')
                ->paginate(20)
                ->withQueryString();

        return Inertia::render(
            'Admin/TransferDocuments/Index',
            [
                'documents' => $documents,

                'filters' => $filters,

                'documentTypes' => TransferDocument::TYPES,
            ]
        );
    }

    public function create(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'generate transfer documents'
            ),
            403
        );

        $applications =
            TransferApplication::query()
                ->with([
                    'currentSchool:id,name',
                    'originZone:id,name,code',
                    'transferBoardDecision.recommendedSchool:id,name,census_number',
                    'transferDocuments:id,transfer_application_id,document_type',
                ])
                ->whereIn(
                    'status',
                    [
                        TransferApplication::STATUS_APPROVED,
                        TransferApplication::STATUS_REJECTED,
                        TransferApplication::STATUS_WAITLISTED,
                    ]
                )
                ->latest('id')
                ->get();

        return Inertia::render(
            'Admin/TransferDocuments/Create',
            [
                'applications' => $applications,

                'documentTypes' => TransferDocument::TYPES,

                'defaultIssuedDate' => now()->toDateString(),
            ]
        );
    }

    public function store(
        StoreTransferDocumentRequest $request
    ): RedirectResponse {
        $application =
            TransferApplication::query()
                ->with(
                    'transferBoardDecision'
                )
                ->findOrFail(
                    $request->integer(
                        'transfer_application_id'
                    )
                );

        $document =
            $this->documentService->create(
                $application,
                $request->user(),
                $request->validated()
            );

        return redirect()
            ->route(
                'admin.transfer-documents.show',
                $document
            )
            ->with(
                'success',
                'Transfer document generated successfully.'
            );
    }

    public function show(
        Request $request,
        TransferDocument $transferDocument
    ): Response {
        abort_unless(
            $request->user()->can(
                'view transfer documents'
            ),
            403
        );

        $transferDocument->load([
            'transferApplication.currentSchool.division.zone',
            'transferApplication.originZone',
            'transferApplication.transferBoardDecision.recommendedSchool.division.zone',
            'issuer:id,name,email',
            'publisher:id,name,email',
        ]);

        return Inertia::render(
            'Admin/TransferDocuments/Show',
            [
                'document' => $transferDocument,
            ]
        );
    }

    public function uploadSigned(
        UploadSignedTransferDocumentRequest $request,
        TransferDocument $transferDocument
    ): RedirectResponse {
        $this->documentService
            ->uploadSignedCopy(
                $transferDocument,
                $request->file(
                    'signed_document'
                )
            );

        return back()->with(
            'success',
            'Signed document uploaded successfully.'
        );
    }

    public function publish(
        PublishTransferDocumentRequest $request,
        TransferDocument $transferDocument
    ): RedirectResponse {
        $this->documentService->publish(
            $transferDocument,
            $request->user()
        );

        return back()->with(
            'success',
            'Transfer result published successfully.'
        );
    }

    public function unpublish(
        Request $request,
        TransferDocument $transferDocument
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'unpublish transfer results'
            ),
            403
        );

        $this->documentService->unpublish(
            $transferDocument
        );

        return back()->with(
            'success',
            'Transfer result unpublished successfully.'
        );
    }

    public function regenerate(
        Request $request,
        TransferDocument $transferDocument
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'generate transfer documents'
            ),
            403
        );

        $this->documentService->regenerate(
            $transferDocument
        );

        return back()->with(
            'success',
            'Transfer document regenerated successfully.'
        );
    }

    public function download(
        Request $request,
        TransferDocument $transferDocument
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'download transfer documents'
            ),
            403
        );

        $path =
            $transferDocument
                ->downloadablePath();

        abort_unless(
            $path
            && Storage::disk('local')
                ->exists($path),
            404
        );

        return response()->download(
            Storage::disk('local')
                ->path($path),
            $this->downloadName(
                $transferDocument
            ),
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    private function downloadName(
        TransferDocument $document
    ): string {
        $type = str(
            $document->document_type
        )
            ->slug()
            ->toString();

        return sprintf(
            '%s-%s.pdf',
            $type,
            $document->document_number
        );
    }
}
