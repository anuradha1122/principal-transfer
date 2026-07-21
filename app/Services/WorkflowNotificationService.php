<?php

namespace App\Services;

use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\TransferDocument;
use App\Models\User;
use App\Notifications\ProvincialDecisionRecordedNotification;
use App\Notifications\TransferAppealWorkflowNotification;
use App\Notifications\TransferApplicationSubmittedNotification;
use App\Notifications\TransferBoardDecisionRecordedNotification;
use App\Notifications\TransferDocumentPublicationNotification;
use App\Notifications\ZonalDecisionRecordedNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

class WorkflowNotificationService
{
    public function __construct(
        private readonly NotificationRecipientService $recipients
    ) {}

    public function applicationSubmitted(
        TransferApplication $application
    ): void {
        $application->loadMissing([
            'principalProfile.user',
            'originZone',
        ]);

        $principalUser =
            $this->principalUser($application);

        $principalName =
            $this->principalName($application);

        $applicationNumber =
            $this->applicationNumber($application);

        $zonalDirectors =
            $this->recipients->zonalDirectors(
                $application->origin_zone_id
            );

        if ($principalUser) {
            $this->notifySafely(
                $principalUser,
                new TransferApplicationSubmittedNotification(
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    recipientType: 'principal',
                    actionUrl: route(
                        'principal.transfer-applications.show',
                        $application
                    )
                ),
                'application_submitted_principal',
                $application->id
            );
        }

        $this->sendSafely(
            $zonalDirectors,
            new TransferApplicationSubmittedNotification(
                applicationNumber: $applicationNumber,
                principalName: $principalName,
                recipientType: 'zonal',
                actionUrl: route(
                    'zonal.transfer-applications.show',
                    $application
                )
            ),
            'application_submitted_zonal',
            $application->id
        );
    }

    public function zonalDecisionRecorded(
        TransferApplication $application,
        string $decision
    ): void {
        $application->loadMissing([
            'principalProfile.user',
        ]);

        $principalUser =
            $this->principalUser($application);

        $principalName =
            $this->principalName($application);

        $applicationNumber =
            $this->applicationNumber($application);

        if ($principalUser) {
            $this->notifySafely(
                $principalUser,
                new ZonalDecisionRecordedNotification(
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    decision: $decision,
                    actionUrl: route(
                        'principal.transfer-applications.show',
                        $application
                    ),
                    recipientType: 'principal'
                ),
                'zonal_decision_principal',
                $application->id
            );
        }

        if ($this->isApproved($decision)) {
            $this->sendSafely(
                $this->recipients->provincialDirectors(),
                new ZonalDecisionRecordedNotification(
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    decision: $decision,
                    actionUrl: route(
                        'provincial.transfer-applications.show',
                        $application
                    ),
                    recipientType: 'provincial'
                ),
                'zonal_decision_provincial',
                $application->id
            );
        }
    }

    public function provincialDecisionRecorded(
        TransferApplication $application,
        string $decision
    ): void {
        $application->loadMissing([
            'principalProfile.user',
        ]);

        $principalUser =
            $this->principalUser($application);

        $principalName =
            $this->principalName($application);

        $applicationNumber =
            $this->applicationNumber($application);

        if ($principalUser) {
            $this->notifySafely(
                $principalUser,
                new ProvincialDecisionRecordedNotification(
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    decision: $decision,
                    actionUrl: route(
                        'principal.transfer-applications.show',
                        $application
                    ),
                    recipientType: 'principal'
                ),
                'provincial_decision_principal',
                $application->id
            );
        }

        if ($this->isApproved($decision)) {
            $this->sendSafely(
                $this->recipients->transferBoardMembers(),
                new ProvincialDecisionRecordedNotification(
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    decision: $decision,
                    actionUrl: route(
                        'transfer-board.transfer-applications.show',
                        $application
                    ),
                    recipientType: 'transfer_board'
                ),
                'provincial_decision_board',
                $application->id
            );
        }

        if ($this->isReturnedToZone($decision)) {
            $this->sendSafely(
                $this->recipients->zonalDirectors(
                    $application->origin_zone_id
                ),
                new ProvincialDecisionRecordedNotification(
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    decision: $decision,
                    actionUrl: route(
                        'zonal.transfer-applications.show',
                        $application
                    ),
                    recipientType: 'zonal'
                ),
                'provincial_returned_to_zone',
                $application->id
            );
        }
    }

    public function transferBoardDecisionRecorded(
        TransferApplication $application,
        string $decision
    ): void {
        $application->loadMissing([
            'principalProfile.user',
        ]);

        $principalUser =
            $this->principalUser($application);

        $principalName =
            $this->principalName($application);

        $applicationNumber =
            $this->applicationNumber($application);

        if ($principalUser) {
            $this->notifySafely(
                $principalUser,
                new TransferBoardDecisionRecordedNotification(
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    decision: $decision,
                    actionUrl: route(
                        'principal.transfer-applications.show',
                        $application
                    ),
                    recipientType: 'principal'
                ),
                'board_decision_principal',
                $application->id
            );
        }

        $this->sendSafely(
            $this->recipients->provincialDirectors(),
            new TransferBoardDecisionRecordedNotification(
                applicationNumber: $applicationNumber,
                principalName: $principalName,
                decision: $decision,
                actionUrl: route(
                    'provincial.transfer-applications.show',
                    $application
                ),
                recipientType: 'provincial'
            ),
            'board_decision_provincial',
            $application->id
        );
    }

    public function appealSubmitted(
        TransferAppeal $appeal
    ): void {
        $this->appealStatusChanged(
            $appeal,
            'submitted'
        );
    }

    public function appealStatusChanged(
        TransferAppeal $appeal,
        string $status
    ): void {
        $appeal->loadMissing([
            'transferApplication.principalProfile.user',
        ]);

        $application =
            $appeal->transferApplication;

        if (! $application) {
            return;
        }

        $principalUser =
            $this->principalUser($application);

        $principalName =
            $this->principalName($application);

        $applicationNumber =
            $this->applicationNumber($application);

        $appealNumber =
            $appeal->appeal_number
            ?? 'APL-'.$appeal->id;

        if ($principalUser) {
            $this->notifySafely(
                $principalUser,
                new TransferAppealWorkflowNotification(
                    appealNumber: $appealNumber,
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    status: $status,
                    actionUrl: route(
                        'principal.transfer-appeals.show',
                        $appeal
                    ),
                    recipientType: 'principal'
                ),
                'appeal_status_principal',
                $appeal->id
            );
        }

        if ($this->shouldNotifyAppealReviewers($status)) {
            $reviewers = $this->mergeUsers(
                $this->recipients->provincialDirectors(),
                $this->recipients->transferBoardMembers()
            );

            $this->sendSafely(
                $reviewers,
                new TransferAppealWorkflowNotification(
                    appealNumber: $appealNumber,
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    status: $status,
                    actionUrl: route(
                        'transfer-board.transfer-appeals.show',
                        $appeal
                    ),
                    recipientType: 'reviewer'
                ),
                'appeal_status_reviewers',
                $appeal->id
            );
        }
    }

    public function documentPublicationChanged(
        TransferDocument $document,
        bool $published
    ): void {
        $document->loadMissing([
            'transferApplication.principalProfile.user',
        ]);

        $application =
            $document->transferApplication;

        if (! $application) {
            return;
        }

        $principalUser =
            $this->principalUser($application);

        $principalName =
            $this->principalName($application);

        $applicationNumber =
            $this->applicationNumber($application);

        $documentNumber =
            $document->document_number
            ?? 'DOC-'.$document->id;

        if (
            $published &&
            $principalUser
        ) {
            $this->notifySafely(
                $principalUser,
                new TransferDocumentPublicationNotification(
                    documentNumber: $documentNumber,
                    applicationNumber: $applicationNumber,
                    principalName: $principalName,
                    published: true,
                    actionUrl: route(
                        'principal.transfer-documents.show',
                        $document
                    ),
                    recipientType: 'principal'
                ),
                'document_published_principal',
                $document->id
            );
        }

        $administrators = $this->mergeUsers(
            $this->recipients->provincialDirectors(),
            $this->recipients->transferBoardMembers()
        );

        $this->sendSafely(
            $administrators,
            new TransferDocumentPublicationNotification(
                documentNumber: $documentNumber,
                applicationNumber: $applicationNumber,
                principalName: $principalName,
                published: $published,
                actionUrl: route(
                    'admin.transfer-documents.show',
                    $document
                ),
                recipientType: 'administrator'
            ),
            $published
                ? 'document_published_administrators'
                : 'document_unpublished_administrators',
            $document->id
        );
    }

    private function principalUser(
        TransferApplication $application
    ): ?User {
        return $application
            ->principalProfile
            ?->user;
    }

    private function principalName(
        TransferApplication $application
    ): string {
        return $application
            ->principalProfile
            ?->full_name
            ?? $application
                ->principalProfile
                ?->user
                ?->name
            ?? 'Principal';
    }

    private function applicationNumber(
        TransferApplication $application
    ): string {
        return $application->application_number
            ?? 'APP-'.$application->id;
    }

    private function isApproved(
        string $decision
    ): bool {
        return in_array(
            strtolower(trim($decision)),
            [
                'approved',
                'zonal approved',
                'zonal_approved',
                'provincial approved',
                'provincial_approved',
            ],
            true
        );
    }

    private function isReturnedToZone(
        string $decision
    ): bool {
        return in_array(
            strtolower(trim($decision)),
            [
                'returned to zone',
                'return to zone',
                'returned_to_zone',
            ],
            true
        );
    }

    private function shouldNotifyAppealReviewers(
        string $status
    ): bool {
        return in_array(
            strtolower(trim($status)),
            [
                'submitted',
                'appeal submitted',
                'appeal_submitted',
                'resubmitted',
                'appeal resubmitted',
                'appeal_resubmitted',
            ],
            true
        );
    }

    private function mergeUsers(
        Collection $first,
        Collection $second
    ): Collection {
        return $first
            ->concat($second)
            ->unique('id')
            ->values();
    }

    private function notifySafely(
        User $user,
        Notification $notification,
        string $event,
        int|string|null $subjectId = null
    ): void {
        try {
            $user->notify($notification);
        } catch (Throwable $exception) {
            Log::warning(
                'Workflow notification failed.',
                [
                    'event' => $event,
                    'subject_id' => $subjectId,
                    'user_id' => $user->id,
                    'message' => $exception->getMessage(),
                ]
            );

            report($exception);
        }
    }

    private function sendSafely(
        Collection $users,
        Notification $notification,
        string $event,
        int|string|null $subjectId = null
    ): void {
        if ($users->isEmpty()) {
            return;
        }

        try {
            NotificationFacade::send(
                $users,
                $notification
            );
        } catch (Throwable $exception) {
            Log::warning(
                'Workflow notification batch failed.',
                [
                    'event' => $event,
                    'subject_id' => $subjectId,
                    'recipient_count' => $users->count(),
                    'message' => $exception->getMessage(),
                ]
            );

            report($exception);
        }
    }
}
