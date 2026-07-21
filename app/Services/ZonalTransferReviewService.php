<?php

namespace App\Services;

use App\Models\TransferApplication;
use App\Models\TransferApplicationAction;
use App\Models\User;
use App\Models\ZonalReview;
use App\Notifications\TransferApplicationZonalReviewStartedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ZonalTransferReviewService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly WorkflowNotificationService $workflowNotifications
    ) {}

    public function startReview(
        TransferApplication $transferApplication,
        User $reviewer
    ): TransferApplication {
        if (! $transferApplication->canStartZonalReview()) {
            throw ValidationException::withMessages([
                'status' => 'Only submitted applications can enter Zonal review.',
            ]);
        }

        $updatedApplication = DB::transaction(
            function () use (
                $transferApplication,
                $reviewer
            ): TransferApplication {
                $lockedApplication = TransferApplication::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $transferApplication->id
                    );

                if (! $lockedApplication->canStartZonalReview()) {
                    throw ValidationException::withMessages([
                        'status' => 'This application is no longer available for review.',
                    ]);
                }

                $fromStatus = $lockedApplication->status;

                $review = ZonalReview::query()->firstOrNew([
                    'transfer_application_id' => $lockedApplication->id,
                ]);

                $review->fill([
                    'zone_id' => $lockedApplication->origin_zone_id,

                    'reviewer_id' => $reviewer->id,

                    'review_started_at' => $review->review_started_at
                        ?? now(),
                ]);

                $review->save();

                $lockedApplication->forceFill([
                    'status' => TransferApplication::STATUS_ZONAL_REVIEW,
                ])->save();

                $this->recordAction(
                    transferApplication: $lockedApplication,

                    actor: $reviewer,

                    action: TransferApplicationAction::ACTION_ZONAL_REVIEW_STARTED,

                    fromStatus: $fromStatus,

                    toStatus: TransferApplication::STATUS_ZONAL_REVIEW,

                    remarks: 'Zonal review started.',

                    metadata: [
                        'zone_id' => $lockedApplication->origin_zone_id,
                    ],
                );

                $this->auditLogService->workflow(
                    'transfer_application.zonal_review_started',
                    $lockedApplication,
                    $fromStatus,
                    TransferApplication::STATUS_ZONAL_REVIEW,
                    [
                        'description' => sprintf(
                            'Zonal review started for transfer application %s.',
                            $lockedApplication
                                ->application_number
                            ?? $lockedApplication->id
                        ),

                        'new_values' => [
                            'status' => TransferApplication::STATUS_ZONAL_REVIEW,

                            'zone_id' => $lockedApplication->origin_zone_id,

                            'reviewer_id' => $reviewer->id,

                            'review_started_at' => $review->review_started_at,
                        ],

                        'metadata' => [
                            'zonal_review_id' => $review->id,

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
                    'zonalReview.reviewer',
                    'actions.actor',
                ]);
            }
        );

        /*
         * Keep the existing principal alert when the Zone starts review.
         */
        $this->sendNotificationSafely(
            $updatedApplication,
            new TransferApplicationZonalReviewStartedNotification(
                $updatedApplication
            )
        );

        return $updatedApplication;
    }

    public function approve(
        TransferApplication $transferApplication,
        User $reviewer,
        array $validated
    ): TransferApplication {
        if (
            ! $transferApplication
                ->canReceiveZonalDecision()
        ) {
            throw ValidationException::withMessages([
                'status' => 'Only applications under Zonal review can be approved.',
            ]);
        }

        $updatedApplication = DB::transaction(
            function () use (
                $transferApplication,
                $reviewer,
                $validated
            ): TransferApplication {
                $lockedApplication =
                    TransferApplication::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $transferApplication->id
                        );

                if (
                    ! $lockedApplication
                        ->canReceiveZonalDecision()
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'This application is no longer awaiting a Zonal decision.',
                    ]);
                }

                $fromStatus =
                    $lockedApplication->status;

                $review =
                    ZonalReview::query()->firstOrNew([
                        'transfer_application_id' => $lockedApplication->id,
                    ]);

                $oldReviewValues = [
                    'recommendation' => $review->recommendation,

                    'decision' => $review->decision,

                    'remarks' => $review->remarks,

                    'rejection_reason' => $review->rejection_reason,
                ];

                $review->fill([
                    'zone_id' => $lockedApplication->origin_zone_id,

                    'reviewer_id' => $reviewer->id,

                    'recommendation' => $validated['recommendation'],

                    'decision' => ZonalReview::DECISION_APPROVED,

                    'remarks' => $validated['remarks']
                        ?? null,

                    'rejection_reason' => null,

                    'review_started_at' => $review->review_started_at
                        ?? now(),

                    'reviewed_at' => now(),
                ]);

                $review->save();

                $lockedApplication->forceFill([
                    'status' => TransferApplication::STATUS_ZONAL_APPROVED,
                ])->save();

                $this->recordAction(
                    transferApplication: $lockedApplication,

                    actor: $reviewer,

                    action: TransferApplicationAction::ACTION_ZONAL_APPROVED,

                    fromStatus: $fromStatus,

                    toStatus: TransferApplication::STATUS_ZONAL_APPROVED,

                    remarks: $validated['remarks']
                        ?? null,

                    metadata: [
                        'zone_id' => $lockedApplication->origin_zone_id,

                        'recommendation' => $validated['recommendation'],

                        'decision' => ZonalReview::DECISION_APPROVED,
                    ],
                );

                $this->auditLogService->workflow(
                    'transfer_application.zonal_approved',
                    $lockedApplication,
                    $fromStatus,
                    TransferApplication::STATUS_ZONAL_APPROVED,
                    [
                        'description' => sprintf(
                            'Transfer application %s was approved at Zonal level.',
                            $lockedApplication
                                ->application_number
                            ?? $lockedApplication->id
                        ),

                        'old_values' => $oldReviewValues,

                        'new_values' => [
                            'status' => TransferApplication::STATUS_ZONAL_APPROVED,

                            'zone_id' => $lockedApplication->origin_zone_id,

                            'reviewer_id' => $reviewer->id,

                            'recommendation' => $review->recommendation,

                            'decision' => $review->decision,

                            'remarks' => $review->remarks,

                            'reviewed_at' => $review->reviewed_at,
                        ],

                        'metadata' => [
                            'zonal_review_id' => $review->id,

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
                    'zonalReview.reviewer',
                    'actions.actor',
                ]);
            }
        );

        /*
         * Notify the Principal and Provincial Directors.
         */
        $this->runWorkflowNotificationSafely(
            function () use (
                $updatedApplication
            ): void {
                $this->workflowNotifications
                    ->zonalDecisionRecorded(
                        $updatedApplication,
                        'approved'
                    );
            },
            $updatedApplication,
            'zonal_approved'
        );

        return $updatedApplication;
    }

    public function reject(
        TransferApplication $transferApplication,
        User $reviewer,
        array $validated
    ): TransferApplication {
        if (
            ! $transferApplication
                ->canReceiveZonalDecision()
        ) {
            throw ValidationException::withMessages([
                'status' => 'Only applications under Zonal review can be rejected.',
            ]);
        }

        $updatedApplication = DB::transaction(
            function () use (
                $transferApplication,
                $reviewer,
                $validated
            ): TransferApplication {
                $lockedApplication =
                    TransferApplication::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $transferApplication->id
                        );

                if (
                    ! $lockedApplication
                        ->canReceiveZonalDecision()
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'This application is no longer awaiting a Zonal decision.',
                    ]);
                }

                $fromStatus =
                    $lockedApplication->status;

                $review =
                    ZonalReview::query()->firstOrNew([
                        'transfer_application_id' => $lockedApplication->id,
                    ]);

                $oldReviewValues = [
                    'recommendation' => $review->recommendation,

                    'decision' => $review->decision,

                    'remarks' => $review->remarks,

                    'rejection_reason' => $review->rejection_reason,
                ];

                $review->fill([
                    'zone_id' => $lockedApplication->origin_zone_id,

                    'reviewer_id' => $reviewer->id,

                    'recommendation' => $validated['recommendation']
                        ?? 'Not Recommended',

                    'decision' => ZonalReview::DECISION_REJECTED,

                    'remarks' => $validated['remarks']
                        ?? null,

                    'rejection_reason' => $validated['rejection_reason'],

                    'review_started_at' => $review->review_started_at
                        ?? now(),

                    'reviewed_at' => now(),
                ]);

                $review->save();

                $lockedApplication->forceFill([
                    'status' => TransferApplication::STATUS_ZONAL_REJECTED,
                ])->save();

                $this->recordAction(
                    transferApplication: $lockedApplication,

                    actor: $reviewer,

                    action: TransferApplicationAction::ACTION_ZONAL_REJECTED,

                    fromStatus: $fromStatus,

                    toStatus: TransferApplication::STATUS_ZONAL_REJECTED,

                    remarks: $validated['rejection_reason'],

                    metadata: [
                        'zone_id' => $lockedApplication->origin_zone_id,

                        'recommendation' => $validated['recommendation']
                            ?? 'Not Recommended',

                        'decision' => ZonalReview::DECISION_REJECTED,
                    ],
                );

                $this->auditLogService->workflow(
                    'transfer_application.zonal_rejected',
                    $lockedApplication,
                    $fromStatus,
                    TransferApplication::STATUS_ZONAL_REJECTED,
                    [
                        'description' => sprintf(
                            'Transfer application %s was rejected at Zonal level.',
                            $lockedApplication
                                ->application_number
                            ?? $lockedApplication->id
                        ),

                        'old_values' => $oldReviewValues,

                        'new_values' => [
                            'status' => TransferApplication::STATUS_ZONAL_REJECTED,

                            'zone_id' => $lockedApplication->origin_zone_id,

                            'reviewer_id' => $reviewer->id,

                            'recommendation' => $review->recommendation,

                            'decision' => $review->decision,

                            'remarks' => $review->remarks,

                            'rejection_reason' => $review->rejection_reason,

                            'reviewed_at' => $review->reviewed_at,
                        ],

                        'metadata' => [
                            'zonal_review_id' => $review->id,

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
                    'zonalReview.reviewer',
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
                    ->zonalDecisionRecorded(
                        $updatedApplication,
                        'rejected'
                    );
            },
            $updatedApplication,
            'zonal_rejected'
        );

        return $updatedApplication;
    }

    private function recordAction(
        TransferApplication $transferApplication,
        User $actor,
        string $action,
        ?string $fromStatus,
        ?string $toStatus,
        ?string $remarks = null,
        ?array $metadata = null
    ): void {
        $transferApplication
            ->actions()
            ->create([
                'action' => $action,

                'from_status' => $fromStatus,

                'to_status' => $toStatus,

                'remarks' => $remarks,

                'acted_by' => $actor->id,

                'acted_at' => now(),

                'metadata' => $metadata,
            ]);
    }

    private function sendNotificationSafely(
        TransferApplication $transferApplication,
        object $notification
    ): void {
        try {
            $recipient =
                $transferApplication
                    ->principalProfile
                    ?->user;

            if ($recipient !== null) {
                $recipient->notify(
                    $notification
                );
            }
        } catch (Throwable $exception) {
            Log::warning(
                'Transfer application Zonal notification failed.',
                [
                    'transfer_application_id' => $transferApplication->id,

                    'notification' => $notification::class,

                    'message' => $exception->getMessage(),
                ]
            );
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
