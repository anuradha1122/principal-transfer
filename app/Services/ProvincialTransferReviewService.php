<?php

namespace App\Services;

use App\Models\ProvincialReview;
use App\Models\TransferApplication;
use App\Models\TransferApplicationAction;
use App\Models\User;
use App\Notifications\TransferApplicationProvincialReviewStartedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProvincialTransferReviewService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly WorkflowNotificationService $workflowNotifications
    ) {}

    public function startReview(
        TransferApplication $application,
        User $reviewer
    ): TransferApplication {
        if (
            ! $application
                ->canEnterProvincialReview()
        ) {
            throw ValidationException::withMessages([
                'status' => 'Only Zonal-approved applications can enter Provincial review.',
            ]);
        }

        $updatedApplication = DB::transaction(
            function () use (
                $application,
                $reviewer
            ): TransferApplication {
                $lockedApplication =
                    TransferApplication::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $application->id
                        );

                if (
                    ! $lockedApplication
                        ->canEnterProvincialReview()
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'This application is no longer available for Provincial review.',
                    ]);
                }

                $fromStatus =
                    $lockedApplication->status;

                $review =
                    ProvincialReview::query()
                        ->firstOrCreate(
                            [
                                'transfer_application_id' => $lockedApplication->id,
                            ],
                            [
                                'reviewer_id' => $reviewer->id,

                                'decision' => ProvincialReview::DECISION_PENDING,

                                'review_started_at' => now(),
                            ]
                        );

                if (
                    $review->decision
                    !== ProvincialReview::DECISION_PENDING
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'This application already has a completed Provincial decision.',
                    ]);
                }

                $review->update([
                    'reviewer_id' => $reviewer->id,

                    'review_started_at' => $review->review_started_at
                        ?? now(),
                ]);

                $lockedApplication->update([
                    'status' => TransferApplication::STATUS_PROVINCIAL_REVIEW,

                    'updated_by' => $reviewer->id,
                ]);

                $this->recordAction(
                    application: $lockedApplication,

                    actor: $reviewer,

                    action: TransferApplicationAction::ACTION_PROVINCIAL_REVIEW_STARTED,

                    fromStatus: $fromStatus,

                    toStatus: TransferApplication::STATUS_PROVINCIAL_REVIEW,

                    remarks: 'Provincial review started.'
                );

                $this->auditLogService->workflow(
                    'transfer_application.provincial_review_started',
                    $lockedApplication,
                    $fromStatus,
                    TransferApplication::STATUS_PROVINCIAL_REVIEW,
                    [
                        'description' => sprintf(
                            'Provincial review started for transfer application %s.',
                            $lockedApplication
                                ->application_number
                            ?? $lockedApplication->id
                        ),

                        'new_values' => [
                            'status' => TransferApplication::STATUS_PROVINCIAL_REVIEW,

                            'reviewer_id' => $reviewer->id,

                            'review_started_at' => $review->review_started_at,

                            'decision' => $review->decision,
                        ],

                        'metadata' => [
                            'provincial_review_id' => $review->id,

                            'reviewer_name' => $reviewer->name,

                            'reviewer_email' => $reviewer->email,
                        ],

                        'user' => $reviewer,
                    ]
                );

                return $lockedApplication->fresh([
                    'principalProfile.user',
                    'transferCycle',
                    'originZone',
                    'provincialReview.reviewer',
                    'actions.actor',
                ]);
            }
        );

        /*
         * Keep the existing alert when Provincial review starts.
         */
        $this->notifyPrincipalSafely(
            $updatedApplication,
            new TransferApplicationProvincialReviewStartedNotification(
                $updatedApplication
            )
        );

        return $updatedApplication;
    }

    public function approve(
        TransferApplication $application,
        User $reviewer,
        array $data
    ): TransferApplication {
        $this->ensureUnderProvincialReview(
            $application
        );

        $updatedApplication = DB::transaction(
            function () use (
                $application,
                $reviewer,
                $data
            ): TransferApplication {
                $lockedApplication =
                    TransferApplication::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $application->id
                        );

                $this->ensureUnderProvincialReview(
                    $lockedApplication
                );

                $fromStatus =
                    $lockedApplication->status;

                $review =
                    ProvincialReview::query()
                        ->where(
                            'transfer_application_id',
                            $lockedApplication->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $oldReviewValues = [
                    'reviewer_id' => $review->reviewer_id,

                    'decision' => $review->decision,

                    'recommendation' => $review->recommendation,

                    'remarks' => $review->remarks,

                    'rejection_reason' => $review->rejection_reason,

                    'return_reason' => $review->return_reason,

                    'reviewed_at' => $review->reviewed_at,
                ];

                $review->update([
                    'reviewer_id' => $reviewer->id,

                    'decision' => ProvincialReview::DECISION_APPROVED,

                    'recommendation' => $data['recommendation'],

                    'remarks' => $data['remarks']
                        ?? null,

                    'rejection_reason' => null,

                    'return_reason' => null,

                    'reviewed_at' => now(),
                ]);

                $lockedApplication->update([
                    'status' => TransferApplication::STATUS_PROVINCIAL_APPROVED,

                    'updated_by' => $reviewer->id,
                ]);

                $this->recordAction(
                    application: $lockedApplication,

                    actor: $reviewer,

                    action: TransferApplicationAction::ACTION_PROVINCIAL_APPROVED,

                    fromStatus: $fromStatus,

                    toStatus: TransferApplication::STATUS_PROVINCIAL_APPROVED,

                    remarks: $data['remarks']
                        ?? $data['recommendation']
                );

                $this->auditLogService->workflow(
                    'transfer_application.provincial_approved',
                    $lockedApplication,
                    $fromStatus,
                    TransferApplication::STATUS_PROVINCIAL_APPROVED,
                    [
                        'description' => sprintf(
                            'Transfer application %s was approved at Provincial level.',
                            $lockedApplication
                                ->application_number
                            ?? $lockedApplication->id
                        ),

                        'old_values' => $oldReviewValues,

                        'new_values' => [
                            'status' => TransferApplication::STATUS_PROVINCIAL_APPROVED,

                            'reviewer_id' => $reviewer->id,

                            'decision' => $review->decision,

                            'recommendation' => $review->recommendation,

                            'remarks' => $review->remarks,

                            'rejection_reason' => $review->rejection_reason,

                            'return_reason' => $review->return_reason,

                            'reviewed_at' => $review->reviewed_at,
                        ],

                        'metadata' => [
                            'provincial_review_id' => $review->id,

                            'reviewer_name' => $reviewer->name,

                            'reviewer_email' => $reviewer->email,
                        ],

                        'user' => $reviewer,
                    ]
                );

                return $lockedApplication->fresh([
                    'principalProfile.user',
                    'transferCycle',
                    'originZone',
                    'provincialReview.reviewer',
                    'actions.actor',
                ]);
            }
        );

        /*
         * Notify the Principal and Transfer Board Members.
         */
        $this->runWorkflowNotificationSafely(
            function () use (
                $updatedApplication
            ): void {
                $this->workflowNotifications
                    ->provincialDecisionRecorded(
                        $updatedApplication,
                        'approved'
                    );
            },
            $updatedApplication,
            'provincial_approved'
        );

        return $updatedApplication;
    }

    public function reject(
        TransferApplication $application,
        User $reviewer,
        array $data
    ): TransferApplication {
        $this->ensureUnderProvincialReview(
            $application
        );

        $updatedApplication = DB::transaction(
            function () use (
                $application,
                $reviewer,
                $data
            ): TransferApplication {
                $lockedApplication =
                    TransferApplication::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $application->id
                        );

                $this->ensureUnderProvincialReview(
                    $lockedApplication
                );

                $fromStatus =
                    $lockedApplication->status;

                $review =
                    ProvincialReview::query()
                        ->where(
                            'transfer_application_id',
                            $lockedApplication->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $oldReviewValues = [
                    'reviewer_id' => $review->reviewer_id,

                    'decision' => $review->decision,

                    'recommendation' => $review->recommendation,

                    'remarks' => $review->remarks,

                    'rejection_reason' => $review->rejection_reason,

                    'return_reason' => $review->return_reason,

                    'reviewed_at' => $review->reviewed_at,
                ];

                $review->update([
                    'reviewer_id' => $reviewer->id,

                    'decision' => ProvincialReview::DECISION_REJECTED,

                    'recommendation' => $data['recommendation']
                        ?? null,

                    'remarks' => $data['remarks']
                        ?? null,

                    'rejection_reason' => $data['rejection_reason'],

                    'return_reason' => null,

                    'reviewed_at' => now(),
                ]);

                $lockedApplication->update([
                    'status' => TransferApplication::STATUS_PROVINCIAL_REJECTED,

                    'updated_by' => $reviewer->id,
                ]);

                $this->recordAction(
                    application: $lockedApplication,

                    actor: $reviewer,

                    action: TransferApplicationAction::ACTION_PROVINCIAL_REJECTED,

                    fromStatus: $fromStatus,

                    toStatus: TransferApplication::STATUS_PROVINCIAL_REJECTED,

                    remarks: $data['rejection_reason']
                );

                $this->auditLogService->workflow(
                    'transfer_application.provincial_rejected',
                    $lockedApplication,
                    $fromStatus,
                    TransferApplication::STATUS_PROVINCIAL_REJECTED,
                    [
                        'description' => sprintf(
                            'Transfer application %s was rejected at Provincial level.',
                            $lockedApplication
                                ->application_number
                            ?? $lockedApplication->id
                        ),

                        'old_values' => $oldReviewValues,

                        'new_values' => [
                            'status' => TransferApplication::STATUS_PROVINCIAL_REJECTED,

                            'reviewer_id' => $reviewer->id,

                            'decision' => $review->decision,

                            'recommendation' => $review->recommendation,

                            'remarks' => $review->remarks,

                            'rejection_reason' => $review->rejection_reason,

                            'return_reason' => $review->return_reason,

                            'reviewed_at' => $review->reviewed_at,
                        ],

                        'metadata' => [
                            'provincial_review_id' => $review->id,

                            'reviewer_name' => $reviewer->name,

                            'reviewer_email' => $reviewer->email,
                        ],

                        'user' => $reviewer,
                    ]
                );

                return $lockedApplication->fresh([
                    'principalProfile.user',
                    'transferCycle',
                    'originZone',
                    'provincialReview.reviewer',
                    'actions.actor',
                ]);
            }
        );

        /*
         * Notify the Principal.
         */
        $this->runWorkflowNotificationSafely(
            function () use (
                $updatedApplication
            ): void {
                $this->workflowNotifications
                    ->provincialDecisionRecorded(
                        $updatedApplication,
                        'rejected'
                    );
            },
            $updatedApplication,
            'provincial_rejected'
        );

        return $updatedApplication;
    }

    public function returnToZone(
        TransferApplication $application,
        User $reviewer,
        array $data
    ): TransferApplication {
        $this->ensureUnderProvincialReview(
            $application
        );

        $updatedApplication = DB::transaction(
            function () use (
                $application,
                $reviewer,
                $data
            ): TransferApplication {
                $lockedApplication =
                    TransferApplication::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $application->id
                        );

                $this->ensureUnderProvincialReview(
                    $lockedApplication
                );

                $fromStatus =
                    $lockedApplication->status;

                $review =
                    ProvincialReview::query()
                        ->where(
                            'transfer_application_id',
                            $lockedApplication->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $oldReviewValues = [
                    'reviewer_id' => $review->reviewer_id,

                    'decision' => $review->decision,

                    'recommendation' => $review->recommendation,

                    'remarks' => $review->remarks,

                    'rejection_reason' => $review->rejection_reason,

                    'return_reason' => $review->return_reason,

                    'reviewed_at' => $review->reviewed_at,
                ];

                $review->update([
                    'reviewer_id' => $reviewer->id,

                    'decision' => ProvincialReview::DECISION_RETURNED_TO_ZONE,

                    'remarks' => $data['remarks']
                        ?? null,

                    'rejection_reason' => null,

                    'return_reason' => $data['return_reason'],

                    'reviewed_at' => now(),
                ]);

                $lockedApplication->update([
                    'status' => TransferApplication::STATUS_RETURNED_TO_ZONE,

                    'updated_by' => $reviewer->id,
                ]);

                $this->recordAction(
                    application: $lockedApplication,

                    actor: $reviewer,

                    action: TransferApplicationAction::ACTION_RETURNED_TO_ZONE,

                    fromStatus: $fromStatus,

                    toStatus: TransferApplication::STATUS_RETURNED_TO_ZONE,

                    remarks: $data['return_reason']
                );

                $this->auditLogService->workflow(
                    'transfer_application.returned_to_zone',
                    $lockedApplication,
                    $fromStatus,
                    TransferApplication::STATUS_RETURNED_TO_ZONE,
                    [
                        'description' => sprintf(
                            'Transfer application %s was returned to the Zone.',
                            $lockedApplication
                                ->application_number
                            ?? $lockedApplication->id
                        ),

                        'old_values' => $oldReviewValues,

                        'new_values' => [
                            'status' => TransferApplication::STATUS_RETURNED_TO_ZONE,

                            'reviewer_id' => $reviewer->id,

                            'decision' => $review->decision,

                            'remarks' => $review->remarks,

                            'rejection_reason' => $review->rejection_reason,

                            'return_reason' => $review->return_reason,

                            'reviewed_at' => $review->reviewed_at,
                        ],

                        'metadata' => [
                            'provincial_review_id' => $review->id,

                            'reviewer_name' => $reviewer->name,

                            'reviewer_email' => $reviewer->email,

                            'origin_zone_id' => $lockedApplication->origin_zone_id,
                        ],

                        'user' => $reviewer,
                    ]
                );

                return $lockedApplication->fresh([
                    'principalProfile.user',
                    'transferCycle',
                    'originZone',
                    'provincialReview.reviewer',
                    'actions.actor',
                ]);
            }
        );

        /*
         * Notify the Principal and assigned Zonal Directors.
         */
        $this->runWorkflowNotificationSafely(
            function () use (
                $updatedApplication
            ): void {
                $this->workflowNotifications
                    ->provincialDecisionRecorded(
                        $updatedApplication,
                        'returned_to_zone'
                    );
            },
            $updatedApplication,
            'returned_to_zone'
        );

        return $updatedApplication;
    }

    private function ensureUnderProvincialReview(
        TransferApplication $application
    ): void {
        if (
            ! $application
                ->isUnderProvincialReview()
        ) {
            throw ValidationException::withMessages([
                'status' => 'The application is not under Provincial review.',
            ]);
        }
    }

    private function recordAction(
        TransferApplication $application,
        User $actor,
        string $action,
        string $fromStatus,
        string $toStatus,
        ?string $remarks = null
    ): void {
        TransferApplicationAction::query()
            ->create([
                'transfer_application_id' => $application->id,

                'action' => $action,

                'from_status' => $fromStatus,

                'to_status' => $toStatus,

                'remarks' => $remarks,

                'acted_by' => $actor->id,

                'acted_at' => now(),
            ]);
    }

    private function notifyPrincipalSafely(
        TransferApplication $application,
        object $notification
    ): void {
        try {
            $application->loadMissing(
                'principalProfile.user'
            );

            $application
                ->principalProfile
                ?->user
                ?->notify(
                    $notification
                );
        } catch (Throwable $exception) {
            Log::warning(
                'Provincial review-start notification failed.',
                [
                    'transfer_application_id' => $application->id,

                    'notification' => $notification::class,

                    'message' => $exception->getMessage(),
                ]
            );

            report($exception);
        }
    }

    private function runWorkflowNotificationSafely(
        callable $callback,
        TransferApplication $transferApplication,
        string $event
    ): void {
        try {
            $callback();
        } catch (Throwable $exception) {
            Log::warning(
                'Transfer application workflow notification failed.',
                [
                    'transfer_application_id' => $transferApplication->id,

                    'event' => $event,

                    'message' => $exception->getMessage(),
                ]
            );

            report($exception);
        }
    }
}
