<?php

namespace App\Services;

use App\Models\ProvincialReview;
use App\Models\TransferApplication;
use App\Models\TransferApplicationAction;
use App\Models\User;
use App\Notifications\TransferApplicationProvincialApprovedNotification;
use App\Notifications\TransferApplicationProvincialRejectedNotification;
use App\Notifications\TransferApplicationProvincialReviewStartedNotification;
use App\Notifications\TransferApplicationReturnedToZoneNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProvincialTransferReviewService
{
    public function startReview(
        TransferApplication $application,
        User $reviewer
    ): TransferApplication {
        if (
            ! $application
                ->canEnterProvincialReview()
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'Only Zonal-approved applications can enter Provincial review.',
            ]);
        }

        return DB::transaction(
            function () use (
                $application,
                $reviewer
            ): TransferApplication {
                $fromStatus =
                    $application->status;

                $review = ProvincialReview::query()
                    ->firstOrCreate(
                        [
                            'transfer_application_id' =>
                                $application->id,
                        ],
                        [
                            'reviewer_id' =>
                                $reviewer->id,

                            'decision' =>
                                ProvincialReview::DECISION_PENDING,

                            'review_started_at' =>
                                now(),
                        ]
                    );

                if (
                    $review->decision
                    !== ProvincialReview::DECISION_PENDING
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'This application already has a completed Provincial decision.',
                    ]);
                }

                $review->update([
                    'reviewer_id' =>
                        $reviewer->id,

                    'review_started_at' =>
                        $review->review_started_at
                        ?? now(),
                ]);

                $application->update([
                    'status' =>
                        TransferApplication::STATUS_PROVINCIAL_REVIEW,

                    'updated_by' =>
                        $reviewer->id,
                ]);

                $this->recordAction(
                    application: $application,
                    actor: $reviewer,
                    action:
                        TransferApplicationAction::ACTION_PROVINCIAL_REVIEW_STARTED,
                    fromStatus: $fromStatus,
                    toStatus:
                        TransferApplication::STATUS_PROVINCIAL_REVIEW,
                    remarks:
                        'Provincial review started.'
                );

                $this->notifyPrincipal(
                    $application,
                    new TransferApplicationProvincialReviewStartedNotification(
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
        $this->ensureUnderProvincialReview(
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

                $review = ProvincialReview::query()
                    ->where(
                        'transfer_application_id',
                        $application->id
                    )
                    ->firstOrFail();

                $review->update([
                    'reviewer_id' =>
                        $reviewer->id,

                    'decision' =>
                        ProvincialReview::DECISION_APPROVED,

                    'recommendation' =>
                        $data['recommendation'],

                    'remarks' =>
                        $data['remarks'] ?? null,

                    'rejection_reason' =>
                        null,

                    'return_reason' =>
                        null,

                    'reviewed_at' =>
                        now(),
                ]);

                $application->update([
                    'status' =>
                        TransferApplication::STATUS_PROVINCIAL_APPROVED,

                    'updated_by' =>
                        $reviewer->id,
                ]);

                $this->recordAction(
                    application: $application,
                    actor: $reviewer,
                    action:
                        TransferApplicationAction::ACTION_PROVINCIAL_APPROVED,
                    fromStatus: $fromStatus,
                    toStatus:
                        TransferApplication::STATUS_PROVINCIAL_APPROVED,
                    remarks:
                        $data['remarks']
                        ?? $data['recommendation']
                );

                $this->notifyPrincipal(
                    $application,
                    new TransferApplicationProvincialApprovedNotification(
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
        $this->ensureUnderProvincialReview(
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

                $review = ProvincialReview::query()
                    ->where(
                        'transfer_application_id',
                        $application->id
                    )
                    ->firstOrFail();

                $review->update([
                    'reviewer_id' =>
                        $reviewer->id,

                    'decision' =>
                        ProvincialReview::DECISION_REJECTED,

                    'recommendation' =>
                        $data['recommendation']
                        ?? null,

                    'remarks' =>
                        $data['remarks'] ?? null,

                    'rejection_reason' =>
                        $data['rejection_reason'],

                    'return_reason' =>
                        null,

                    'reviewed_at' =>
                        now(),
                ]);

                $application->update([
                    'status' =>
                        TransferApplication::STATUS_PROVINCIAL_REJECTED,

                    'updated_by' =>
                        $reviewer->id,
                ]);

                $this->recordAction(
                    application: $application,
                    actor: $reviewer,
                    action:
                        TransferApplicationAction::ACTION_PROVINCIAL_REJECTED,
                    fromStatus: $fromStatus,
                    toStatus:
                        TransferApplication::STATUS_PROVINCIAL_REJECTED,
                    remarks:
                        $data['rejection_reason']
                );

                $this->notifyPrincipal(
                    $application,
                    new TransferApplicationProvincialRejectedNotification(
                        $application
                    )
                );

                return $application->fresh();
            }
        );
    }

    public function returnToZone(
        TransferApplication $application,
        User $reviewer,
        array $data
    ): TransferApplication {
        $this->ensureUnderProvincialReview(
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

                $review = ProvincialReview::query()
                    ->where(
                        'transfer_application_id',
                        $application->id
                    )
                    ->firstOrFail();

                $review->update([
                    'reviewer_id' =>
                        $reviewer->id,

                    'decision' =>
                        ProvincialReview::DECISION_RETURNED_TO_ZONE,

                    'remarks' =>
                        $data['remarks'] ?? null,

                    'rejection_reason' =>
                        null,

                    'return_reason' =>
                        $data['return_reason'],

                    'reviewed_at' =>
                        now(),
                ]);

                $application->update([
                    'status' =>
                        TransferApplication::STATUS_RETURNED_TO_ZONE,

                    'updated_by' =>
                        $reviewer->id,
                ]);

                $this->recordAction(
                    application: $application,
                    actor: $reviewer,
                    action:
                        TransferApplicationAction::ACTION_RETURNED_TO_ZONE,
                    fromStatus: $fromStatus,
                    toStatus:
                        TransferApplication::STATUS_RETURNED_TO_ZONE,
                    remarks:
                        $data['return_reason']
                );

                $this->notifyZoneDirectors(
                    $application,
                    new TransferApplicationReturnedToZoneNotification(
                        $application
                    )
                );

                $this->notifyPrincipal(
                    $application,
                    new TransferApplicationReturnedToZoneNotification(
                        $application
                    )
                );

                return $application->fresh();
            }
        );
    }

    private function ensureUnderProvincialReview(
        TransferApplication $application
    ): void {
        if (
            ! $application
                ->isUnderProvincialReview()
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'The application is not under Provincial review.',
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

    private function notifyZoneDirectors(
        TransferApplication $application,
        object $notification
    ): void {
        User::query()
            ->role('Zonal Director')
            ->where(
                'assigned_zone_id',
                $application->origin_zone_id
            )
            ->where(
                'is_active',
                true
            )
            ->get()
            ->each(
                fn (User $user) =>
                    $user->notify($notification)
            );
    }
}
