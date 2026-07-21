<?php

namespace App\Services;

use App\Models\TransferAppeal;
use App\Models\TransferAppealAction;
use App\Models\TransferAppealDocument;
use App\Models\TransferApplication;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TransferAppealService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly WorkflowNotificationService $workflowNotifications
    ) {}

    public function createDraft(
        TransferApplication $application,
        User $user,
        array $data,
        array $documents = []
    ): TransferAppeal {
        $this->ensureApplicationMayBeAppealed($application);

        return DB::transaction(function () use (
            $application,
            $user,
            $data,
            $documents
        ): TransferAppeal {
            $appeal = TransferAppeal::query()->create([
                'transfer_application_id' => $application->id,
                'principal_profile_id' => $application->principal_profile_id,
                'appeal_number' => $this->generateAppealNumber(),
                'appeal_reason' => $data['appeal_reason'],
                'appeal_details' => $data['appeal_details'],
                'requested_outcome' => $data['requested_outcome'],
                'status' => TransferAppeal::STATUS_DRAFT,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $this->storeDocuments(
                $appeal,
                $documents,
                $user
            );

            $this->recordAction(
                $appeal,
                'Appeal Draft Created',
                null,
                TransferAppeal::STATUS_DRAFT,
                null,
                $user
            );

            $this->auditLogService->workflow(
                'transfer_appeal.draft_created',
                $appeal,
                null,
                TransferAppeal::STATUS_DRAFT,
                [
                    'description' => sprintf(
                        'Transfer appeal %s was created as a draft.',
                        $appeal->appeal_number
                    ),
                    'parent' => $application,
                    'new_values' => [
                        'appeal_number' => $appeal->appeal_number,
                        'appeal_reason' => $appeal->appeal_reason,
                        'appeal_details' => $appeal->appeal_details,
                        'requested_outcome' => $appeal->requested_outcome,
                        'status' => $appeal->status,
                    ],
                    'metadata' => [
                        'uploaded_document_count' => count(
                            array_filter(
                                $documents,
                                fn ($document): bool => $document instanceof UploadedFile
                            )
                        ),
                    ],
                    'user' => $user,
                ]
            );

            return $appeal->fresh([
                'documents',
                'transferApplication',
            ]);
        });
    }

    public function updateDraft(
        TransferAppeal $appeal,
        User $user,
        array $data,
        array $documents = []
    ): TransferAppeal {
        if (! $appeal->isDraft()) {
            throw ValidationException::withMessages([
                'appeal' => 'Only Draft appeals may be edited.',
            ]);
        }

        return DB::transaction(function () use (
            $appeal,
            $user,
            $data,
            $documents
        ): TransferAppeal {
            $oldValues = [
                'appeal_reason' => $appeal->appeal_reason,
                'appeal_details' => $appeal->appeal_details,
                'requested_outcome' => $appeal->requested_outcome,
            ];

            $appeal->update([
                'appeal_reason' => $data['appeal_reason'],
                'appeal_details' => $data['appeal_details'],
                'requested_outcome' => $data['requested_outcome'],
                'updated_by' => $user->id,
            ]);

            $this->storeDocuments(
                $appeal,
                $documents,
                $user
            );

            $this->recordAction(
                $appeal,
                'Appeal Draft Updated',
                TransferAppeal::STATUS_DRAFT,
                TransferAppeal::STATUS_DRAFT,
                null,
                $user
            );

            $this->auditLogService->workflow(
                'transfer_appeal.draft_updated',
                $appeal,
                TransferAppeal::STATUS_DRAFT,
                TransferAppeal::STATUS_DRAFT,
                [
                    'description' => sprintf(
                        'Transfer appeal %s draft was updated.',
                        $appeal->appeal_number
                    ),
                    'parent' => $appeal->transferApplication,
                    'old_values' => $oldValues,
                    'new_values' => [
                        'appeal_reason' => $appeal->appeal_reason,
                        'appeal_details' => $appeal->appeal_details,
                        'requested_outcome' => $appeal->requested_outcome,
                    ],
                    'metadata' => [
                        'uploaded_document_count' => count(
                            array_filter(
                                $documents,
                                fn ($document): bool => $document instanceof UploadedFile
                            )
                        ),
                    ],
                    'user' => $user,
                ]
            );

            return $appeal->fresh([
                'documents',
                'transferApplication',
            ]);
        });
    }

    public function submit(
        TransferAppeal $appeal,
        User $user
    ): TransferAppeal {
        if (! $appeal->canBeSubmitted()) {
            throw ValidationException::withMessages([
                'appeal' => 'This appeal cannot be submitted from its current status.',
            ]);
        }

        $this->ensureApplicationMayBeAppealed(
            $appeal->transferApplication,
            $appeal->id
        );

        $appeal = DB::transaction(
            function () use (
                $appeal,
                $user
            ): TransferAppeal {
                $fromStatus = $appeal->status;

                $appeal->update([
                    'status' => TransferAppeal::STATUS_SUBMITTED,
                    'submitted_at' => now(),
                    'updated_by' => $user->id,
                ]);

                $this->recordAction(
                    $appeal,
                    'Appeal Submitted',
                    $fromStatus,
                    TransferAppeal::STATUS_SUBMITTED,
                    null,
                    $user
                );

                $this->auditLogService->workflow(
                    'transfer_appeal.submitted',
                    $appeal,
                    $fromStatus,
                    TransferAppeal::STATUS_SUBMITTED,
                    [
                        'description' => sprintf(
                            'Transfer appeal %s was submitted.',
                            $appeal->appeal_number
                        ),
                        'parent' => $appeal->transferApplication,
                        'new_values' => [
                            'status' => TransferAppeal::STATUS_SUBMITTED,
                            'submitted_at' => $appeal->submitted_at,
                        ],
                        'user' => $user,
                    ]
                );

                return $appeal->fresh([
                    'documents',
                    'transferApplication',
                    'principalProfile.user',
                ]);
            }
        );

        $this->runWorkflowNotificationSafely(
            $appeal,
            'submitted'
        );

        return $appeal;
    }

    public function startReview(
        TransferAppeal $appeal,
        User $reviewer
    ): TransferAppeal {
        if (! $appeal->isSubmittedForReview()) {
            throw ValidationException::withMessages([
                'appeal' => 'Only Submitted or Resubmitted appeals may enter review.',
            ]);
        }

        return DB::transaction(
            function () use (
                $appeal,
                $reviewer
            ): TransferAppeal {
                $fromStatus = $appeal->status;

                $appeal->update([
                    'status' => TransferAppeal::STATUS_UNDER_REVIEW,
                    'review_started_at' => now(),
                    'reviewer_id' => $reviewer->id,
                    'updated_by' => $reviewer->id,
                ]);

                $this->recordAction(
                    $appeal,
                    'Appeal Review Started',
                    $fromStatus,
                    TransferAppeal::STATUS_UNDER_REVIEW,
                    null,
                    $reviewer
                );

                $this->auditLogService->workflow(
                    'transfer_appeal.review_started',
                    $appeal,
                    $fromStatus,
                    TransferAppeal::STATUS_UNDER_REVIEW,
                    [
                        'description' => sprintf(
                            'Review started for transfer appeal %s.',
                            $appeal->appeal_number
                        ),
                        'parent' => $appeal->transferApplication,
                        'new_values' => [
                            'status' => TransferAppeal::STATUS_UNDER_REVIEW,
                            'review_started_at' => $appeal->review_started_at,
                            'reviewer_id' => $reviewer->id,
                        ],
                        'metadata' => [
                            'reviewer_name' => $reviewer->name,
                            'reviewer_email' => $reviewer->email,
                        ],
                        'user' => $reviewer,
                    ]
                );

                return $appeal->fresh([
                    'transferApplication',
                    'reviewer',
                ]);
            }
        );
    }

    public function returnForClarification(
        TransferAppeal $appeal,
        User $reviewer,
        string $clarificationRequest
    ): TransferAppeal {
        $this->ensureUnderReview($appeal);

        $appeal = DB::transaction(
            function () use (
                $appeal,
                $reviewer,
                $clarificationRequest
            ): TransferAppeal {
                $fromStatus = $appeal->status;

                $appeal->update([
                    'status' => TransferAppeal::STATUS_RETURNED,
                    'returned_at' => now(),
                    'clarification_request' => $clarificationRequest,
                    'clarification_response' => null,
                    'reviewer_id' => $reviewer->id,
                    'updated_by' => $reviewer->id,
                ]);

                $this->recordAction(
                    $appeal,
                    'Returned for Clarification',
                    $fromStatus,
                    TransferAppeal::STATUS_RETURNED,
                    $clarificationRequest,
                    $reviewer
                );

                $this->auditLogService->workflow(
                    'transfer_appeal.returned_for_clarification',
                    $appeal,
                    $fromStatus,
                    TransferAppeal::STATUS_RETURNED,
                    [
                        'description' => sprintf(
                            'Transfer appeal %s was returned for clarification.',
                            $appeal->appeal_number
                        ),
                        'parent' => $appeal->transferApplication,
                        'new_values' => [
                            'status' => TransferAppeal::STATUS_RETURNED,
                            'returned_at' => $appeal->returned_at,
                            'clarification_request' => $clarificationRequest,
                        ],
                        'user' => $reviewer,
                    ]
                );

                return $appeal->fresh([
                    'transferApplication',
                    'principalProfile.user',
                    'reviewer',
                ]);
            }
        );

        $this->runWorkflowNotificationSafely(
            $appeal,
            'returned_for_clarification'
        );

        return $appeal;
    }

    public function clarifyAndResubmit(
        TransferAppeal $appeal,
        User $user,
        string $response,
        array $documents = []
    ): TransferAppeal {
        if (! $appeal->isReturned()) {
            throw ValidationException::withMessages([
                'appeal' => 'Only an appeal returned for clarification may be resubmitted.',
            ]);
        }

        $appeal = DB::transaction(
            function () use (
                $appeal,
                $user,
                $response,
                $documents
            ): TransferAppeal {
                $fromStatus = $appeal->status;

                $appeal->update([
                    'status' => TransferAppeal::STATUS_RESUBMITTED,
                    'clarification_response' => $response,
                    'resubmitted_at' => now(),
                    'updated_by' => $user->id,
                ]);

                $this->storeDocuments(
                    $appeal,
                    $documents,
                    $user
                );

                $this->recordAction(
                    $appeal,
                    'Clarification Submitted and Appeal Resubmitted',
                    $fromStatus,
                    TransferAppeal::STATUS_RESUBMITTED,
                    $response,
                    $user
                );

                $this->auditLogService->workflow(
                    'transfer_appeal.resubmitted',
                    $appeal,
                    $fromStatus,
                    TransferAppeal::STATUS_RESUBMITTED,
                    [
                        'description' => sprintf(
                            'Transfer appeal %s was resubmitted with clarification.',
                            $appeal->appeal_number
                        ),
                        'parent' => $appeal->transferApplication,
                        'new_values' => [
                            'status' => TransferAppeal::STATUS_RESUBMITTED,
                            'clarification_response' => $response,
                            'resubmitted_at' => $appeal->resubmitted_at,
                        ],
                        'metadata' => [
                            'uploaded_document_count' => count(
                                array_filter(
                                    $documents,
                                    fn ($document): bool => $document instanceof UploadedFile
                                )
                            ),
                        ],
                        'user' => $user,
                    ]
                );

                return $appeal->fresh([
                    'documents',
                    'transferApplication',
                    'principalProfile.user',
                ]);
            }
        );

        $this->runWorkflowNotificationSafely(
            $appeal,
            'resubmitted'
        );

        return $appeal;
    }

    public function approve(
        TransferAppeal $appeal,
        User $reviewer,
        array $data
    ): TransferAppeal {
        $this->ensureUnderReview($appeal);

        $appeal = DB::transaction(
            function () use (
                $appeal,
                $reviewer,
                $data
            ): TransferAppeal {
                $fromStatus = $appeal->status;

                /*
                 * Existing published documents are preserved but unpublished.
                 * A revised document must later be generated as a new version.
                 */
                $publishedDocuments = $appeal
                    ->transferApplication
                    ->transferDocuments()
                    ->where('is_published', true)
                    ->get();

                $unpublishedDocumentIds = $publishedDocuments
                    ->pluck('id')
                    ->values()
                    ->all();

                $appeal
                    ->transferApplication
                    ->transferDocuments()
                    ->where('is_published', true)
                    ->update([
                        'is_published' => false,
                        'published_at' => null,
                    ]);

                $appeal->update([
                    'status' => TransferAppeal::STATUS_APPROVED,
                    'decision_outcome' => TransferAppeal::DECISION_APPROVED,
                    'decision_remarks' => $data['decision_remarks'],
                    'rejection_reason' => null,
                    'revised_school_id' => $data['revised_school_id'] ?? null,
                    'revised_effective_date' => $data['revised_effective_date'] ?? null,
                    'revised_appointment_type' => $data['revised_appointment_type'] ?? null,
                    'revised_decision_reference' => $data['revised_decision_reference'],
                    'reviewer_id' => $reviewer->id,
                    'decided_at' => now(),
                    'updated_by' => $reviewer->id,
                ]);

                $this->recordAction(
                    $appeal,
                    'Appeal Approved',
                    $fromStatus,
                    TransferAppeal::STATUS_APPROVED,
                    $data['decision_remarks'],
                    $reviewer,
                    [
                        'revised_school_id' => $data['revised_school_id'] ?? null,
                        'revised_effective_date' => $data['revised_effective_date'] ?? null,
                        'revised_appointment_type' => $data['revised_appointment_type'] ?? null,
                        'revised_decision_reference' => $data['revised_decision_reference'],
                    ]
                );

                $this->auditLogService->workflow(
                    'transfer_appeal.approved',
                    $appeal,
                    $fromStatus,
                    TransferAppeal::STATUS_APPROVED,
                    [
                        'description' => sprintf(
                            'Transfer appeal %s was approved.',
                            $appeal->appeal_number
                        ),
                        'parent' => $appeal->transferApplication,
                        'new_values' => [
                            'status' => TransferAppeal::STATUS_APPROVED,
                            'decision_outcome' => TransferAppeal::DECISION_APPROVED,
                            'decision_remarks' => $appeal->decision_remarks,
                            'revised_school_id' => $appeal->revised_school_id,
                            'revised_effective_date' => $appeal->revised_effective_date,
                            'revised_appointment_type' => $appeal->revised_appointment_type,
                            'revised_decision_reference' => $appeal->revised_decision_reference,
                            'decided_at' => $appeal->decided_at,
                        ],
                        'metadata' => [
                            'unpublished_document_ids' => $unpublishedDocumentIds,
                            'revised_document_required' => true,
                        ],
                        'user' => $reviewer,
                    ]
                );

                foreach ($publishedDocuments as $document) {
                    $this->auditLogService->document(
                        'transfer_document.unpublished_after_appeal_approval',
                        $document,
                        [
                            'description' => sprintf(
                                'Transfer document %s was unpublished because appeal %s was approved.',
                                $document->id,
                                $appeal->appeal_number
                            ),
                            'parent' => $appeal->transferApplication,
                            'old_values' => [
                                'is_published' => true,
                                'published_at' => $document->published_at,
                            ],
                            'new_values' => [
                                'is_published' => false,
                                'published_at' => null,
                            ],
                            'metadata' => [
                                'transfer_appeal_id' => $appeal->id,
                                'appeal_number' => $appeal->appeal_number,
                            ],
                            'user' => $reviewer,
                        ]
                    );
                }

                return $appeal->fresh([
                    'revisedSchool',
                    'transferApplication',
                    'principalProfile.user',
                    'reviewer',
                ]);
            }
        );

        $this->runWorkflowNotificationSafely(
            $appeal,
            'approved'
        );

        return $appeal;
    }

    public function reject(
        TransferAppeal $appeal,
        User $reviewer,
        array $data
    ): TransferAppeal {
        $this->ensureUnderReview($appeal);

        $appeal = DB::transaction(
            function () use (
                $appeal,
                $reviewer,
                $data
            ): TransferAppeal {
                $fromStatus = $appeal->status;

                $appeal->update([
                    'status' => TransferAppeal::STATUS_REJECTED,
                    'decision_outcome' => TransferAppeal::DECISION_REJECTED,
                    'decision_remarks' => $data['decision_remarks'] ?? null,
                    'rejection_reason' => $data['rejection_reason'],
                    'reviewer_id' => $reviewer->id,
                    'decided_at' => now(),
                    'updated_by' => $reviewer->id,
                ]);

                $this->recordAction(
                    $appeal,
                    'Appeal Rejected',
                    $fromStatus,
                    TransferAppeal::STATUS_REJECTED,
                    $data['rejection_reason'],
                    $reviewer
                );

                $this->auditLogService->workflow(
                    'transfer_appeal.rejected',
                    $appeal,
                    $fromStatus,
                    TransferAppeal::STATUS_REJECTED,
                    [
                        'description' => sprintf(
                            'Transfer appeal %s was rejected.',
                            $appeal->appeal_number
                        ),
                        'parent' => $appeal->transferApplication,
                        'new_values' => [
                            'status' => TransferAppeal::STATUS_REJECTED,
                            'decision_outcome' => TransferAppeal::DECISION_REJECTED,
                            'decision_remarks' => $appeal->decision_remarks,
                            'rejection_reason' => $appeal->rejection_reason,
                            'decided_at' => $appeal->decided_at,
                        ],
                        'user' => $reviewer,
                    ]
                );

                return $appeal->fresh([
                    'transferApplication',
                    'principalProfile.user',
                    'reviewer',
                ]);
            }
        );

        $this->runWorkflowNotificationSafely(
            $appeal,
            'rejected'
        );

        return $appeal;
    }

    public function withdraw(
        TransferAppeal $appeal,
        User $user,
        ?string $remarks = null
    ): TransferAppeal {
        if (! $appeal->canBeWithdrawn()) {
            throw ValidationException::withMessages([
                'appeal' => 'This appeal can no longer be withdrawn.',
            ]);
        }

        return DB::transaction(
            function () use (
                $appeal,
                $user,
                $remarks
            ): TransferAppeal {
                $fromStatus = $appeal->status;

                $appeal->update([
                    'status' => TransferAppeal::STATUS_WITHDRAWN,
                    'withdrawn_at' => now(),
                    'updated_by' => $user->id,
                ]);

                $this->recordAction(
                    $appeal,
                    'Appeal Withdrawn',
                    $fromStatus,
                    TransferAppeal::STATUS_WITHDRAWN,
                    $remarks,
                    $user
                );

                $this->auditLogService->workflow(
                    'transfer_appeal.withdrawn',
                    $appeal,
                    $fromStatus,
                    TransferAppeal::STATUS_WITHDRAWN,
                    [
                        'description' => sprintf(
                            'Transfer appeal %s was withdrawn.',
                            $appeal->appeal_number
                        ),
                        'parent' => $appeal->transferApplication,
                        'new_values' => [
                            'status' => TransferAppeal::STATUS_WITHDRAWN,
                            'withdrawn_at' => $appeal->withdrawn_at,
                        ],
                        'metadata' => [
                            'remarks' => $remarks,
                        ],
                        'user' => $user,
                    ]
                );

                return $appeal->fresh([
                    'transferApplication',
                    'principalProfile.user',
                ]);
            }
        );
    }

    public function deleteDraft(
        TransferAppeal $appeal,
        ?User $user = null
    ): void {
        if (! $appeal->isDraft()) {
            throw ValidationException::withMessages([
                'appeal' => 'Only Draft appeals may be deleted.',
            ]);
        }

        DB::transaction(function () use (
            $appeal,
            $user
        ): void {
            $appeal->loadMissing([
                'documents',
                'transferApplication',
            ]);

            $documentMetadata = $appeal->documents
                ->map(
                    fn (
                        TransferAppealDocument $document
                    ): array => [
                        'id' => $document->id,
                        'document_name' => $document->document_name,
                        'original_name' => $document->original_name,
                        'mime_type' => $document->mime_type,
                        'file_size' => $document->file_size,
                    ]
                )
                ->values()
                ->all();

            $this->auditLogService->workflow(
                'transfer_appeal.draft_deleted',
                $appeal,
                TransferAppeal::STATUS_DRAFT,
                null,
                [
                    'description' => sprintf(
                        'Transfer appeal %s draft was deleted.',
                        $appeal->appeal_number
                    ),
                    'parent' => $appeal->transferApplication,
                    'old_values' => [
                        'appeal_number' => $appeal->appeal_number,
                        'appeal_reason' => $appeal->appeal_reason,
                        'appeal_details' => $appeal->appeal_details,
                        'requested_outcome' => $appeal->requested_outcome,
                        'status' => $appeal->status,
                    ],
                    'metadata' => [
                        'documents' => $documentMetadata,
                    ],
                    'user' => $user,
                ]
            );

            foreach ($appeal->documents as $document) {
                Storage::disk(
                    $document->disk
                )->delete(
                    $document->file_path
                );
            }

            $appeal->delete();
        });
    }

    public function deleteDocument(
        TransferAppealDocument $document,
        ?User $user = null
    ): void {
        DB::transaction(function () use (
            $document,
            $user
        ): void {
            $document->loadMissing([
                'transferAppeal.transferApplication',
            ]);

            $appeal = $document->transferAppeal;

            $this->auditLogService->document(
                'transfer_appeal.document_deleted',
                $document,
                [
                    'description' => sprintf(
                        'Supporting document %s was deleted from appeal %s.',
                        $document->original_name,
                        $appeal?->appeal_number ?? $document->transfer_appeal_id
                    ),
                    'parent' => $appeal?->transferApplication,
                    'old_values' => [
                        'document_name' => $document->document_name,
                        'original_name' => $document->original_name,
                        'mime_type' => $document->mime_type,
                        'file_size' => $document->file_size,
                    ],
                    'metadata' => [
                        'transfer_appeal_id' => $document->transfer_appeal_id,
                        'appeal_number' => $appeal?->appeal_number,
                    ],
                    'user' => $user,
                ]
            );

            Storage::disk(
                $document->disk
            )->delete(
                $document->file_path
            );

            $document->delete();
        });
    }

    public function ensureApplicationMayBeAppealed(
        TransferApplication $application,
        ?int $excludedAppealId = null
    ): void {
        $application->loadMissing([
            'transferBoardDecision',
            'transferDocuments',
        ]);

        if (! in_array(
            $application->status,
            [
                'Approved',
                'Rejected',
                'Waitlisted',
            ],
            true
        )) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'Only finalized transfer applications may be appealed.',
            ]);
        }

        $publishedDocument = $application
            ->transferDocuments
            ->where('is_published', true)
            ->sortByDesc('published_at')
            ->first();

        if (! $publishedDocument) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'The final result must be published before an appeal can be created.',
            ]);
        }

        $deadlineDays = config(
            'transfer-appeals.deadline_days',
            30
        );

        /** @var CarbonInterface|null $publishedAt */
        $publishedAt = $publishedDocument->published_at;

        if (! $publishedAt) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'The result publication date is unavailable.',
            ]);
        }

        if (
            now()->greaterThan(
                $publishedAt
                    ->copy()
                    ->addDays($deadlineDays)
            )
        ) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'The deadline for submitting an appeal has passed.',
            ]);
        }

        $activeAppealQuery = TransferAppeal::query()
            ->where(
                'transfer_application_id',
                $application->id
            )
            ->whereIn(
                'status',
                TransferAppeal::activeStatuses()
            );

        if ($excludedAppealId) {
            $activeAppealQuery->whereKeyNot(
                $excludedAppealId
            );
        }

        if ($activeAppealQuery->exists()) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'An active appeal already exists for this transfer application.',
            ]);
        }
    }

    private function ensureUnderReview(
        TransferAppeal $appeal
    ): void {
        if (
            $appeal->status
            !== TransferAppeal::STATUS_UNDER_REVIEW
        ) {
            throw ValidationException::withMessages([
                'appeal' => 'This action is available only while the appeal is Under Review.',
            ]);
        }
    }

    private function generateAppealNumber(): string
    {
        $year = now()->format('Y');

        $latestId = TransferAppeal::query()
            ->lockForUpdate()
            ->max('id') ?? 0;

        return sprintf(
            'APL-%s-%06d',
            $year,
            $latestId + 1
        );
    }

    private function storeDocuments(
        TransferAppeal $appeal,
        array $documents,
        User $user
    ): void {
        foreach (
            $documents as $index => $uploadedFile
        ) {
            if (
                ! $uploadedFile instanceof UploadedFile
            ) {
                continue;
            }

            $path = $uploadedFile->store(
                'transfer-appeals/'.$appeal->id,
                'local'
            );

            $document = TransferAppealDocument::query()->create([
                'transfer_appeal_id' => $appeal->id,
                'document_name' => 'Supporting Document '.($index + 1),
                'original_name' => $uploadedFile->getClientOriginalName(),
                'file_path' => $path,
                'disk' => 'local',
                'mime_type' => $uploadedFile->getMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'uploaded_by' => $user->id,
            ]);

            $this->auditLogService->document(
                'transfer_appeal.document_uploaded',
                $document,
                [
                    'description' => sprintf(
                        'Supporting document %s was uploaded for appeal %s.',
                        $document->original_name,
                        $appeal->appeal_number
                    ),
                    'parent' => $appeal->transferApplication,
                    'new_values' => [
                        'document_name' => $document->document_name,
                        'original_name' => $document->original_name,
                        'mime_type' => $document->mime_type,
                        'file_size' => $document->file_size,
                    ],
                    'metadata' => [
                        'transfer_appeal_id' => $appeal->id,
                        'appeal_number' => $appeal->appeal_number,
                    ],
                    'user' => $user,
                ]
            );
        }
    }

    private function recordAction(
        TransferAppeal $appeal,
        string $action,
        ?string $fromStatus,
        ?string $toStatus,
        ?string $remarks,
        User $user,
        ?array $metadata = null
    ): void {
        TransferAppealAction::query()->create([
            'transfer_appeal_id' => $appeal->id,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'remarks' => $remarks,
            'acted_by' => $user->id,
            'acted_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    private function runWorkflowNotificationSafely(
        TransferAppeal $appeal,
        string $status
    ): void {
        try {
            $this->workflowNotifications
                ->appealStatusChanged(
                    $appeal,
                    $status
                );
        } catch (\Throwable $exception) {
            Log::warning(
                'Transfer appeal workflow notification failed.',
                [
                    'appeal_id' => $appeal->id,

                    'status' => $status,

                    'message' => $exception->getMessage(),
                ]
            );

            report($exception);
        }
    }
}
