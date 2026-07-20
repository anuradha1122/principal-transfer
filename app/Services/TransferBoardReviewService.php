<?php

namespace App\Services;

use App\Models\TransferApplication;
use App\Models\TransferApplicationAction;
use App\Models\TransferBoardDecision;
use App\Models\User;
use App\Notifications\TransferApplicationApprovedNotification;
use App\Notifications\TransferApplicationBoardReviewStartedNotification;
use App\Notifications\TransferApplicationRejectedNotification;
use App\Notifications\TransferApplicationWaitlistedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferBoardReviewService
{
    public function startReview(
        TransferApplication $application,
        User $reviewer
    ): TransferApplication {
        if (! $application->canEnterBoardReview()) {
            throw ValidationException::withMessages([
                'status' =>
                    'Only Provincially approved applications can enter Transfer Board review.',
            ]);
        }

        return DB::transaction(
            function () use (
                $application,
                $reviewer
            ): TransferApplication {
                $fromStatus =
                    $application->status;

                $decision =
                    TransferBoardDecision::query()
                        ->firstOrCreate(
                            [
                                'transfer_application_id' =>
                                    $application->id,
                            ],
                            [
                                'reviewer_id' =>
                                    $reviewer->id,

                                'decision' =>
                                    TransferBoardDecision::DECISION_PENDING,

                                'review_started_at' =>
                                    now(),
                            ]
                        );

                if (
                    $decision->decision
                    !== TransferBoardDecision::DECISION_PENDING
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'This application already has a final Transfer Board decision.',
                    ]);
                }

                $decision->update([
                    'reviewer_id' =>
                        $reviewer->id,

                    'review_started_at' =>
                        $decision->review_started_at
                        ?? now(),
                ]);

                $application->update([
                    'status' =>
                        TransferApplication::STATUS_BOARD_REVIEW,

                    'updated_by' =>
                        $reviewer->id,
                ]);

                $this->recordAction(
                    application: $application,
                    actor: $reviewer,
                    action:
                        TransferApplicationAction::ACTION_BOARD_REVIEW_STARTED,
                    fromStatus: $fromStatus,
                    toStatus:
                        TransferApplication::STATUS_BOARD_REVIEW,
                    remarks:
                        'Transfer Board review started.'
                );

                $this->notifyPrincipal(
                    $application,
                    new TransferApplicationBoardReviewStartedNotification(
                        $application
                    )
                );

                return $application->fresh();
            }
        );
    }

    public function approve(
        TransferApplication $application,
        User $reviewer,
        array $data
    ): TransferApplication {
        $this->ensureUnderBoardReview(
            $application
        );

        return DB::transaction(
            function () use (
                $application,
                $reviewer,
                $data
            ): TransferApplication {
                $fromStatus =
                    $application->status;

                $decision =
                    $this->decisionRecord(
                        $application
                    );

                $decision->update([
                    'reviewer_id' =>
                        $reviewer->id,

                    'decision' =>
                        TransferBoardDecision::DECISION_APPROVED,

                    'recommended_school_id' =>
                        $data['recommended_school_id'],

                    'effective_date' =>
                        $data['effective_date'],

                    'appointment_type' =>
                        $data['appointment_type'],

                    'decision_reference' =>
                        $data['decision_reference'],

                    'remarks' =>
                        $data['remarks'] ?? null,

                    'rejection_reason' =>
                        null,

                    'waitlist_reason' =>
                        null,

                    'decided_at' =>
                        now(),
                ]);

                $application->update([
                    'status' =>
                        TransferApplication::STATUS_APPROVED,

                    'updated_by' =>
                        $reviewer->id,
                ]);

                $this->recordAction(
                    application: $application,
                    actor: $reviewer,
                    action:
                        TransferApplicationAction::ACTION_BOARD_APPROVED,
                    fromStatus: $fromStatus,
                    toStatus:
                        TransferApplication::STATUS_APPROVED,
                    remarks:
                        $data['remarks']
                        ?? $data['decision_reference']
                );

                $this->notifyPrincipal(
                    $application,
                    new TransferApplicationApprovedNotification(
                        $application
                    )
                );

                return $application->fresh();
            }
        );
    }

    public function reject(
        TransferApplication $application,
        User $reviewer,
        array $data
    ): TransferApplication {
        $this->ensureUnderBoardReview(
            $application
        );

        return DB::transaction(
            function () use (
                $application,
                $reviewer,
                $data
            ): TransferApplication {
                $fromStatus =
                    $application->status;

                $decision =
                    $this->decisionRecord(
                        $application
                    );

                $decision->update([
                    'reviewer_id' =>
                        $reviewer->id,

                    'decision' =>
                        TransferBoardDecision::DECISION_REJECTED,

                    'recommended_school_id' =>
                        null,

                    'effective_date' =>
                        null,

                    'appointment_type' =>
                        null,

                    'decision_reference' =>
                        $data['decision_reference'],

                    'remarks' =>
                        $data['remarks'] ?? null,

                    'rejection_reason' =>
                        $data['rejection_reason'],

                    'waitlist_reason' =>
                        null,

                    'decided_at' =>
                        now(),
                ]);

                $application->update([
                    'status' =>
                        TransferApplication::STATUS_REJECTED,

                    'updated_by' =>
                        $reviewer->id,
                ]);

                $this->recordAction(
                    application: $application,
                    actor: $reviewer,
                    action:
                        TransferApplicationAction::ACTION_BOARD_REJECTED,
                    fromStatus: $fromStatus,
                    toStatus:
                        TransferApplication::STATUS_REJECTED,
                    remarks:
                        $data['rejection_reason']
                );

                $this->notifyPrincipal(
                    $application,
                    new TransferApplicationRejectedNotification(
                        $application
                    )
                );

                return $application->fresh();
            }
        );
    }

    public function waitlist(
        TransferApplication $application,
        User $reviewer,
        array $data
    ): TransferApplication {
        $this->ensureUnderBoardReview(
            $application
        );

        return DB::transaction(
            function () use (
                $application,
                $reviewer,
                $data
            ): TransferApplication {
                $fromStatus =
                    $application->status;

                $decision =
                    $this->decisionRecord(
                        $application
                    );

                $decision->update([
                    'reviewer_id' =>
                        $reviewer->id,

                    'decision' =>
                        TransferBoardDecision::DECISION_WAITLISTED,

                    'recommended_school_id' =>
                        null,

                    'effective_date' =>
                        null,

                    'appointment_type' =>
                        null,

                    'decision_reference' =>
                        $data['decision_reference'],

                    'remarks' =>
                        $data['remarks'] ?? null,

                    'rejection_reason' =>
                        null,

                    'waitlist_reason' =>
                        $data['waitlist_reason'],

                    'decided_at' =>
                        now(),
                ]);

                $application->update([
                    'status' =>
                        TransferApplication::STATUS_WAITLISTED,

                    'updated_by' =>
                        $reviewer->id,
                ]);

                $this->recordAction(
                    application: $application,
                    actor: $reviewer,
                    action:
                        TransferApplicationAction::ACTION_BOARD_WAITLISTED,
                    fromStatus: $fromStatus,
                    toStatus:
                        TransferApplication::STATUS_WAITLISTED,
                    remarks:
                        $data['waitlist_reason']
                );

                $this->notifyPrincipal(
                    $application,
                    new TransferApplicationWaitlistedNotification(
                        $application
                    )
                );

                return $application->fresh();
            }
        );
    }

    private function ensureUnderBoardReview(
        TransferApplication $application
    ): void {
        if (
            ! $application
                ->canReceiveBoardDecision()
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'The application is not under Transfer Board review.',
            ]);
        }
    }

    private function decisionRecord(
        TransferApplication $application
    ): TransferBoardDecision {
        return TransferBoardDecision::query()
            ->where(
                'transfer_application_id',
                $application->id
            )
            ->firstOrFail();
    }

    private function recordAction(
        TransferApplication $application,
        User $actor,
        string $action,
        string $fromStatus,
        string $toStatus,
        ?string $remarks = null
    ): void {
        TransferApplicationAction::create([
            'transfer_application_id' =>
                $application->id,

            'action' =>
                $action,

            'from_status' =>
                $fromStatus,

            'to_status' =>
                $toStatus,

            'remarks' =>
                $remarks,

            'acted_by' =>
                $actor->id,

            'acted_at' =>
                now(),
        ]);
    }

    private function notifyPrincipal(
        TransferApplication $application,
        object $notification
    ): void {
        $application->loadMissing(
            'principalProfile.user'
        );

        $application
            ->principalProfile
            ?->user
            ?->notify($notification);
    }
}
