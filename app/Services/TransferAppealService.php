<?php

namespace App\Services;

use App\Models\TransferAppeal;
use App\Models\TransferAppealAction;
use App\Models\TransferAppealDocument;
use App\Models\TransferApplication;
use App\Models\User;
use App\Notifications\TransferAppealApprovedNotification;
use App\Notifications\TransferAppealRejectedNotification;
use App\Notifications\TransferAppealResubmittedNotification;
use App\Notifications\TransferAppealReturnedNotification;
use App\Notifications\TransferAppealSubmittedNotification;
use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TransferAppealService
{
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
        ) {
            $appeal = TransferAppeal::create([
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

            $this->storeDocuments($appeal, $documents, $user);

            $this->recordAction(
                $appeal,
                'Appeal Draft Created',
                null,
                TransferAppeal::STATUS_DRAFT,
                null,
                $user
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
        ) {
            $appeal->update([
                'appeal_reason' => $data['appeal_reason'],
                'appeal_details' => $data['appeal_details'],
                'requested_outcome' => $data['requested_outcome'],
                'updated_by' => $user->id,
            ]);

            $this->storeDocuments($appeal, $documents, $user);

            $this->recordAction(
                $appeal,
                'Appeal Draft Updated',
                TransferAppeal::STATUS_DRAFT,
                TransferAppeal::STATUS_DRAFT,
                null,
                $user
            );

            return $appeal->fresh('documents');
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

        $appeal = DB::transaction(function () use ($appeal, $user) {
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

            return $appeal->fresh();
        });

        $this->notifyReviewers(
            new TransferAppealSubmittedNotification($appeal)
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

        return DB::transaction(function () use ($appeal, $reviewer) {
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

            return $appeal->fresh();
        });
    }

    public function returnForClarification(
        TransferAppeal $appeal,
        User $reviewer,
        string $clarificationRequest
    ): TransferAppeal {
        $this->ensureUnderReview($appeal);

        $appeal = DB::transaction(function () use (
            $appeal,
            $reviewer,
            $clarificationRequest
        ) {
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

            return $appeal->fresh();
        });

        $this->notifyPrincipal(
            $appeal,
            new TransferAppealReturnedNotification($appeal)
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

        $appeal = DB::transaction(function () use (
            $appeal,
            $user,
            $response,
            $documents
        ) {
            $fromStatus = $appeal->status;

            $appeal->update([
                'status' => TransferAppeal::STATUS_RESUBMITTED,
                'clarification_response' => $response,
                'resubmitted_at' => now(),
                'updated_by' => $user->id,
            ]);

            $this->storeDocuments($appeal, $documents, $user);

            $this->recordAction(
                $appeal,
                'Clarification Submitted and Appeal Resubmitted',
                $fromStatus,
                TransferAppeal::STATUS_RESUBMITTED,
                $response,
                $user
            );

            return $appeal->fresh('documents');
        });

        $this->notifyReviewers(
            new TransferAppealResubmittedNotification($appeal)
        );

        return $appeal;
    }

    public function approve(
        TransferAppeal $appeal,
        User $reviewer,
        array $data
    ): TransferAppeal {
        $this->ensureUnderReview($appeal);

        $appeal = DB::transaction(function () use (
            $appeal,
            $reviewer,
            $data
        ) {
            $fromStatus = $appeal->status;

            /*
             * Existing documents are preserved but unpublished. A revised
             * document must later be generated as a new record/version.
             */
            $appeal->transferApplication
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

            return $appeal->fresh([
                'revisedSchool',
                'transferApplication',
            ]);
        });

        $this->notifyPrincipal(
            $appeal,
            new TransferAppealApprovedNotification($appeal)
        );

        return $appeal;
    }

    public function reject(
        TransferAppeal $appeal,
        User $reviewer,
        array $data
    ): TransferAppeal {
        $this->ensureUnderReview($appeal);

        $appeal = DB::transaction(function () use (
            $appeal,
            $reviewer,
            $data
        ) {
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

            return $appeal->fresh();
        });

        $this->notifyPrincipal(
            $appeal,
            new TransferAppealRejectedNotification($appeal)
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

        return DB::transaction(function () use (
            $appeal,
            $user,
            $remarks
        ) {
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

            return $appeal->fresh();
        });
    }

    public function deleteDraft(TransferAppeal $appeal): void
    {
        if (! $appeal->isDraft()) {
            throw ValidationException::withMessages([
                'appeal' => 'Only Draft appeals may be deleted.',
            ]);
        }

        DB::transaction(function () use ($appeal) {
            foreach ($appeal->documents as $document) {
                Storage::disk($document->disk)->delete($document->file_path);
            }

            $appeal->delete();
        });
    }

    public function deleteDocument(
        TransferAppealDocument $document
    ): void {
        DB::transaction(function () use ($document) {
            Storage::disk($document->disk)->delete($document->file_path);
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

        if (! in_array($application->status, [
            'Approved',
            'Rejected',
            'Waitlisted',
        ], true)) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'Only finalized transfer applications may be appealed.',
            ]);
        }

        $publishedDocument = $application->transferDocuments
            ->where('is_published', true)
            ->sortByDesc('published_at')
            ->first();

        if (! $publishedDocument) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'The final result must be published before an appeal can be created.',
            ]);
        }

        $deadlineDays = config('transfer-appeals.deadline_days', 30);

        /** @var CarbonInterface|null $publishedAt */
        $publishedAt = $publishedDocument->published_at;

        if (! $publishedAt) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'The result publication date is unavailable.',
            ]);
        }

        if (now()->greaterThan($publishedAt->copy()->addDays($deadlineDays))) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'The deadline for submitting an appeal has passed.',
            ]);
        }

        $activeAppealQuery = TransferAppeal::query()
            ->where('transfer_application_id', $application->id)
            ->whereIn('status', TransferAppeal::activeStatuses());

        if ($excludedAppealId) {
            $activeAppealQuery->whereKeyNot($excludedAppealId);
        }

        if ($activeAppealQuery->exists()) {
            throw ValidationException::withMessages([
                'transfer_application_id' => 'An active appeal already exists for this transfer application.',
            ]);
        }
    }

    private function ensureUnderReview(TransferAppeal $appeal): void
    {
        if ($appeal->status !== TransferAppeal::STATUS_UNDER_REVIEW) {
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
        foreach ($documents as $index => $uploadedFile) {
            if (! $uploadedFile instanceof UploadedFile) {
                continue;
            }

            $path = $uploadedFile->store(
                'transfer-appeals/'.$appeal->id,
                'local'
            );

            TransferAppealDocument::create([
                'transfer_appeal_id' => $appeal->id,
                'document_name' => 'Supporting Document '.($index + 1),
                'original_name' => $uploadedFile->getClientOriginalName(),
                'file_path' => $path,
                'disk' => 'local',
                'mime_type' => $uploadedFile->getMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'uploaded_by' => $user->id,
            ]);
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
        TransferAppealAction::create([
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

    private function notifyPrincipal(
        TransferAppeal $appeal,
        object $notification
    ): void {
        try {
            $user = $appeal->principalProfile?->user;

            if ($user) {
                $user->notify($notification);
            }
        } catch (\Throwable $exception) {
            Log::warning('Transfer appeal principal notification failed.', [
                'appeal_id' => $appeal->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private function notifyReviewers(object $notification): void
    {
        try {
            $reviewers = User::query()
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', [
                        'Super Admin',
                        'Provincial Director',
                        'Transfer Board Member',
                    ]);
                })
                ->where('is_active', true)
                ->get();

            Notification::send($reviewers, $notification);
        } catch (\Throwable $exception) {
            Log::warning('Transfer appeal reviewer notification failed.', [
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
