<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\PrincipalProfile;
use App\Models\School;
use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\TransferDocument;
use App\Models\User;
use App\Models\Zone;
use App\Notifications\ProvincialDecisionRecordedNotification;
use App\Notifications\TransferAppealWorkflowNotification;
use App\Notifications\TransferApplicationSubmittedNotification;
use App\Notifications\TransferBoardDecisionRecordedNotification;
use App\Notifications\TransferDocumentPublicationNotification;
use App\Notifications\ZonalDecisionRecordedNotification;
use App\Services\WorkflowNotificationService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkflowNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $principalUser;

    private User $zonalDirector;

    private User $otherZonalDirector;

    private User $provincialDirector;

    private User $boardMember;

    private Zone $originZone;

    private Zone $otherZone;

    private TransferApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Super Admin');
        Role::findOrCreate('Principal');
        Role::findOrCreate('Zonal Director');
        Role::findOrCreate('Provincial Director');
        Role::findOrCreate('Transfer Board Member');
        Role::findOrCreate('Data Entry Officer');

        $this->seed(
            RolePermissionSeeder::class
        );

        $this->originZone =
            Zone::factory()->create([
                'name' => 'Ratnapura',
                'code' => 'RAT',
            ]);

        $this->otherZone =
            Zone::factory()->create([
                'name' => 'Kegalle',
                'code' => 'KEG',
            ]);

        $this->principalUser =
            $this->createUserWithRole(
                'Principal'
            );

        $this->zonalDirector =
            $this->createUserWithRole(
                'Zonal Director',
                [
                    'assigned_zone_id' => $this->originZone->id,
                ]
            );

        $this->otherZonalDirector =
            $this->createUserWithRole(
                'Zonal Director',
                [
                    'assigned_zone_id' => $this->otherZone->id,
                ]
            );

        $this->provincialDirector =
            $this->createUserWithRole(
                'Provincial Director'
            );

        $this->boardMember =
            $this->createUserWithRole(
                'Transfer Board Member'
            );

        $this->application =
            $this->createTransferApplication();
    }

    public function test_application_submission_notifies_principal_and_assigned_zonal_director(): void
    {
        Notification::fake();

        $this->workflowService()
            ->applicationSubmitted(
                $this->application
            );

        Notification::assertSentTo(
            $this->principalUser,
            TransferApplicationSubmittedNotification::class,
            function (
                TransferApplicationSubmittedNotification $notification
            ): bool {
                $data =
                    $notification->toDatabase(
                        $this->principalUser
                    );

                return data_get(
                    $data,
                    'metadata.recipient_type'
                ) === 'principal'
                    && data_get(
                        $data,
                        'metadata.application_number'
                    ) === $this->application
                        ->application_number;
            }
        );

        Notification::assertSentTo(
            $this->zonalDirector,
            TransferApplicationSubmittedNotification::class,
            function (
                TransferApplicationSubmittedNotification $notification
            ): bool {
                $data =
                    $notification->toDatabase(
                        $this->zonalDirector
                    );

                return data_get(
                    $data,
                    'metadata.recipient_type'
                ) === 'zonal';
            }
        );

        Notification::assertNotSentTo(
            $this->otherZonalDirector,
            TransferApplicationSubmittedNotification::class
        );
    }

    public function test_zonal_approval_notifies_principal_and_provincial_director(): void
    {
        Notification::fake();

        $this->workflowService()
            ->zonalDecisionRecorded(
                $this->application,
                'approved'
            );

        Notification::assertSentTo(
            $this->principalUser,
            ZonalDecisionRecordedNotification::class,
            function (
                ZonalDecisionRecordedNotification $notification
            ): bool {
                return data_get(
                    $notification->toDatabase(
                        $this->principalUser
                    ),
                    'metadata.decision'
                ) === 'approved';
            }
        );

        Notification::assertSentTo(
            $this->provincialDirector,
            ZonalDecisionRecordedNotification::class
        );

        Notification::assertNotSentTo(
            $this->boardMember,
            ZonalDecisionRecordedNotification::class
        );
    }

    public function test_zonal_rejection_notifies_only_principal(): void
    {
        Notification::fake();

        $this->workflowService()
            ->zonalDecisionRecorded(
                $this->application,
                'rejected'
            );

        Notification::assertSentTo(
            $this->principalUser,
            ZonalDecisionRecordedNotification::class
        );

        Notification::assertNotSentTo(
            $this->provincialDirector,
            ZonalDecisionRecordedNotification::class
        );

        Notification::assertNotSentTo(
            $this->boardMember,
            ZonalDecisionRecordedNotification::class
        );
    }

    public function test_provincial_approval_notifies_principal_and_transfer_board_member(): void
    {
        Notification::fake();

        $this->workflowService()
            ->provincialDecisionRecorded(
                $this->application,
                'approved'
            );

        Notification::assertSentTo(
            $this->principalUser,
            ProvincialDecisionRecordedNotification::class
        );

        Notification::assertSentTo(
            $this->boardMember,
            ProvincialDecisionRecordedNotification::class
        );

        Notification::assertNotSentTo(
            $this->zonalDirector,
            ProvincialDecisionRecordedNotification::class
        );
    }

    public function test_return_to_zone_notifies_principal_and_only_assigned_zonal_director(): void
    {
        Notification::fake();

        $this->workflowService()
            ->provincialDecisionRecorded(
                $this->application,
                'returned_to_zone'
            );

        Notification::assertSentTo(
            $this->principalUser,
            ProvincialDecisionRecordedNotification::class
        );

        Notification::assertSentTo(
            $this->zonalDirector,
            ProvincialDecisionRecordedNotification::class
        );

        Notification::assertNotSentTo(
            $this->otherZonalDirector,
            ProvincialDecisionRecordedNotification::class
        );

        Notification::assertNotSentTo(
            $this->boardMember,
            ProvincialDecisionRecordedNotification::class
        );
    }

    public function test_transfer_board_decision_notifies_principal_and_provincial_director(): void
    {
        Notification::fake();

        $this->workflowService()
            ->transferBoardDecisionRecorded(
                $this->application,
                'approved'
            );

        Notification::assertSentTo(
            $this->principalUser,
            TransferBoardDecisionRecordedNotification::class
        );

        Notification::assertSentTo(
            $this->provincialDirector,
            TransferBoardDecisionRecordedNotification::class
        );

        Notification::assertNotSentTo(
            $this->zonalDirector,
            TransferBoardDecisionRecordedNotification::class
        );
    }

    public function test_appeal_submission_notifies_principal_and_reviewers(): void
    {
        Notification::fake();

        $appeal = $this->createAppeal(
            TransferAppeal::STATUS_SUBMITTED
        );

        $this->workflowService()
            ->appealSubmitted(
                $appeal
            );

        Notification::assertSentTo(
            $this->principalUser,
            TransferAppealWorkflowNotification::class,
            function (
                TransferAppealWorkflowNotification $notification
            ): bool {
                return data_get(
                    $notification->toDatabase(
                        $this->principalUser
                    ),
                    'metadata.status'
                ) === 'submitted';
            }
        );

        Notification::assertSentTo(
            $this->provincialDirector,
            TransferAppealWorkflowNotification::class
        );

        Notification::assertSentTo(
            $this->boardMember,
            TransferAppealWorkflowNotification::class
        );

        Notification::assertNotSentTo(
            $this->zonalDirector,
            TransferAppealWorkflowNotification::class
        );
    }

    public function test_appeal_resubmission_notifies_principal_and_reviewers(): void
    {
        Notification::fake();

        $appeal = $this->createAppeal(
            TransferAppeal::STATUS_RESUBMITTED
        );

        $this->workflowService()
            ->appealStatusChanged(
                $appeal,
                'resubmitted'
            );

        Notification::assertSentTo(
            $this->principalUser,
            TransferAppealWorkflowNotification::class
        );

        Notification::assertSentTo(
            $this->provincialDirector,
            TransferAppealWorkflowNotification::class
        );

        Notification::assertSentTo(
            $this->boardMember,
            TransferAppealWorkflowNotification::class
        );
    }

    public function test_appeal_decision_notifies_principal_but_not_reviewers(): void
    {
        Notification::fake();

        $appeal = $this->createAppeal(
            TransferAppeal::STATUS_APPROVED
        );

        $this->workflowService()
            ->appealStatusChanged(
                $appeal,
                'approved'
            );

        Notification::assertSentTo(
            $this->principalUser,
            TransferAppealWorkflowNotification::class
        );

        Notification::assertNotSentTo(
            $this->provincialDirector,
            TransferAppealWorkflowNotification::class
        );

        Notification::assertNotSentTo(
            $this->boardMember,
            TransferAppealWorkflowNotification::class
        );
    }

    public function test_document_publication_notifies_principal_and_administrative_reviewers(): void
    {
        Notification::fake();

        $document = $this->createDocument(
            true
        );

        $this->workflowService()
            ->documentPublicationChanged(
                $document,
                true
            );

        Notification::assertSentTo(
            $this->principalUser,
            TransferDocumentPublicationNotification::class,
            function (
                TransferDocumentPublicationNotification $notification
            ): bool {
                return data_get(
                    $notification->toDatabase(
                        $this->principalUser
                    ),
                    'metadata.published'
                ) === true;
            }
        );

        Notification::assertSentTo(
            $this->provincialDirector,
            TransferDocumentPublicationNotification::class
        );

        Notification::assertSentTo(
            $this->boardMember,
            TransferDocumentPublicationNotification::class
        );
    }

    public function test_document_unpublication_does_not_notify_principal(): void
    {
        Notification::fake();

        $document = $this->createDocument(
            false
        );

        $this->workflowService()
            ->documentPublicationChanged(
                $document,
                false
            );

        Notification::assertNotSentTo(
            $this->principalUser,
            TransferDocumentPublicationNotification::class
        );

        Notification::assertSentTo(
            $this->provincialDirector,
            TransferDocumentPublicationNotification::class
        );

        Notification::assertSentTo(
            $this->boardMember,
            TransferDocumentPublicationNotification::class
        );
    }

    public function test_inactive_recipients_are_excluded(): void
    {
        Notification::fake();

        $inactiveProvincialDirector =
            $this->createUserWithRole(
                'Provincial Director',
                [
                    'is_active' => false,
                ]
            );

        $this->workflowService()
            ->zonalDecisionRecorded(
                $this->application,
                'approved'
            );

        Notification::assertNotSentTo(
            $inactiveProvincialDirector,
            ZonalDecisionRecordedNotification::class
        );

        Notification::assertSentTo(
            $this->provincialDirector,
            ZonalDecisionRecordedNotification::class
        );
    }

    private function workflowService(): WorkflowNotificationService
    {
        return app(
            WorkflowNotificationService::class
        );
    }

    private function createUserWithRole(
        string $role,
        array $attributes = []
    ): User {
        $user = User::factory()->create(
            array_merge(
                [
                    'email_verified_at' => now(),
                    'is_active' => true,
                ],
                $attributes
            )
        );

        $user->assignRole(
            $role
        );

        return $user;
    }

    private function createTransferApplication(): TransferApplication
    {
        $division =
            Division::factory()->create([
                'zone_id' => $this->originZone->id,
            ]);

        $school =
            School::factory()->create([
                'division_id' => $division->id,
            ]);

        $profile =
            PrincipalProfile::factory()->create([
                'user_id' => $this->principalUser->id,
            ]);

        $cycle =
            TransferCycle::factory()->create();

        return TransferApplication::factory()
            ->create([
                'principal_profile_id' => $profile->id,

                'transfer_cycle_id' => $cycle->id,

                'current_school_id' => $school->id,

                'origin_zone_id' => $this->originZone->id,

                'application_number' => 'TR-2026-000001',

                'principal_name' => $profile->full_name
                    ?? $this->principalUser->name,

                'nic' => $profile->nic
                    ?? '901234567V',

                'employee_number' => $profile->employee_number
                    ?? 'EMP0001',

                'current_designation' => 'Principal',

                'service_grade' => $profile->service_grade
                    ?? 'SLPS II',

                'current_appointment_start_date' => now()
                    ->subYears(8)
                    ->toDateString(),

                'current_school_service_months' => 96,

                'transfer_reason' => 'Long Service',

                'reason_details' => 'Created for workflow notification testing.',

                'has_medical_reason' => false,

                'has_spouse_employment_reason' => false,

                'is_mutual_transfer' => false,

                'status' => TransferApplication::STATUS_SUBMITTED,

                'submitted_at' => now(),

                'declaration_accepted' => true,
            ])
            ->load([
                'principalProfile.user',
                'originZone',
            ]);
    }

    private function createAppeal(
        string $status
    ): TransferAppeal {
        return TransferAppeal::factory()
            ->create([
                'transfer_application_id' => $this->application->id,

                'principal_profile_id' => $this->application
                    ->principal_profile_id,

                'appeal_number' => 'APL-2026-000001',

                'status' => $status,

                'created_by' => $this->principalUser->id,

                'updated_by' => $this->principalUser->id,
            ])
            ->load([
                'transferApplication.principalProfile.user',
            ]);
    }

    private function createDocument(
        bool $published
    ): TransferDocument {
        return TransferDocument::factory()
            ->create([
                'transfer_application_id' => $this->application->id,

                'document_number' => 'TD-2026-000001',

                'is_published' => $published,

                'published_at' => $published
                        ? now()
                        : null,

                'published_by' => $published
                        ? $this
                            ->provincialDirector
                            ->id
                        : null,
            ])
            ->load([
                'transferApplication.principalProfile.user',
            ]);
    }
}
