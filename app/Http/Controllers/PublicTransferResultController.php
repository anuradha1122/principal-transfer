<?php

namespace App\Http\Controllers;

use App\Models\TransferDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicTransferResultController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'decision' => [
                'nullable',
                'string',
                'in:Approved,Rejected,Waitlisted',
            ],
        ]);

        $documents =
            TransferDocument::query()
                ->with([
                    'transferApplication:id,application_number,principal_name,nic,status,current_school_id',
                    'transferApplication.currentSchool:id,name',
                    'transferApplication.transferBoardDecision:transfer_application_id,recommended_school_id,effective_date',
                    'transferApplication.transferBoardDecision.recommendedSchool:id,name',
                ])
                ->where(
                    'is_published',
                    true
                )
                ->when(
                    $filters['search']
                        ?? null,
                    function (
                        Builder $query,
                        string $search
                    ): void {
                        $query->whereHas(
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
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $filters['decision']
                        ?? null,
                    fn (
                        Builder $query,
                        string $decision
                    ): Builder =>
                        $query->whereHas(
                            'transferApplication',
                            fn (
                                Builder $applicationQuery
                            ) =>
                                $applicationQuery->where(
                                    'status',
                                    $decision
                                )
                        )
                )
                ->latest('published_at')
                ->paginate(20)
                ->withQueryString();

        $documents->getCollection()
            ->transform(
                function (
                    TransferDocument $document
                ): TransferDocument {
                    $application =
                        $document
                            ->transferApplication;

                    if ($application) {
                        $application->nic =
                            $this->maskNic(
                                $application->nic
                            );
                    }

                    return $document;
                }
            );

        return Inertia::render(
            'Public/TransferResults/Index',
            [
                'documents' =>
                    $documents,

                'filters' =>
                    $filters,
            ]
        );
    }

    public function show(
        TransferDocument $transferDocument
    ): Response {
        abort_unless(
            $transferDocument
                ->is_published,
            404
        );

        $transferDocument->load([
            'transferApplication.currentSchool',
            'transferApplication.transferBoardDecision.recommendedSchool',
        ]);

        $application =
            $transferDocument
                ->transferApplication;

        $application->nic =
            $this->maskNic(
                $application->nic
            );

        unset(
            $application->reason_details,
            $application->principal_remarks,
            $application->mutual_principal_nic
        );

        return Inertia::render(
            'Public/TransferResults/Show',
            [
                'document' =>
                    $transferDocument,
            ]
        );
    }

    private function maskNic(
        ?string $nic
    ): ?string {
        if (! $nic) {
            return null;
        }

        $length =
            mb_strlen($nic);

        if ($length <= 4) {
            return str_repeat(
                '*',
                $length
            );
        }

        return mb_substr(
            $nic,
            0,
            $length - 4
        ).'****';
    }
}
