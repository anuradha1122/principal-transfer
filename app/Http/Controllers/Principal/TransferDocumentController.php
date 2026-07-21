<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\TransferDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferDocumentController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'view own transfer documents'
            ),
            403
        );

        $profile =
            $request->user()
                ->principalProfile;

        abort_unless(
            $profile,
            403,
            'Principal profile not found.'
        );

        $documents =
            TransferDocument::query()
                ->with([
                    'transferApplication:id,application_number,principal_profile_id,status',
                ])
                ->whereHas(
                    'transferApplication',
                    fn ($query) => $query->where(
                        'principal_profile_id',
                        $profile->id
                    )
                )
                ->where(
                    'is_published',
                    true
                )
                ->latest('published_at')
                ->paginate(15);

        return Inertia::render(
            'Principal/TransferDocuments/Index',
            [
                'documents' => $documents,
            ]
        );
    }

    public function show(
        Request $request,
        TransferDocument $transferDocument
    ): Response {
        $this->ensureOwnership(
            $request,
            $transferDocument
        );

        $transferDocument->load([
            'transferApplication.currentSchool',
            'transferApplication.transferBoardDecision.recommendedSchool',
            'issuer:id,name',
        ]);

        return Inertia::render(
            'Principal/TransferDocuments/Show',
            [
                'document' => $transferDocument,
            ]
        );
    }

    public function download(
        Request $request,
        TransferDocument $transferDocument
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'download own transfer documents'
            ),
            403
        );

        $this->ensureOwnership(
            $request,
            $transferDocument
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
            str(
                $transferDocument
                    ->document_type
            )
                ->slug()
                ->append(
                    "-{$transferDocument->document_number}.pdf"
                )
                ->toString(),
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    private function ensureOwnership(
        Request $request,
        TransferDocument $document
    ): void {
        $profile =
            $request->user()
                ->principalProfile;

        abort_unless(
            $profile,
            403
        );

        $document->loadMissing(
            'transferApplication'
        );

        abort_unless(
            $document->is_published
            && $document
                ->transferApplication
                ->principal_profile_id
                === $profile->id,
            403
        );
    }
}
