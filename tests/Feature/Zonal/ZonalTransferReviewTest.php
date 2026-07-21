<?php

namespace Tests\Feature\Zonal;

use App\Models\Division;
use App\Models\PrincipalProfile;
use App\Models\School;
use App\Models\TransferApplication;
use App\Models\TransferApplicationAction;
use App\Models\TransferCycle;
use App\Models\User;
use App\Models\ZonalReview;
use App\Models\Zone;
use App\Notifications\TransferApplicationZonalReviewStartedNotification;
use App\Notifications\ZonalDecisionRecordedNotification;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ZonalTransferReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $zonalDirector;

    private User $principalUser;

    private Zone $assignedZone;

    private Zone $otherZone;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Super Admin');
        Role::findOrCreate('Principal');
        Role::findOrCreate('Zonal Director');

        $this->seed(
            RolePermissionSeeder::class
        );

        $this->assignedZone =
            Zone::factory()->create([
                'name' => 'Ratnapura',
                'code' => 'RAT',
            ]);

        $this->otherZone =
            Zone::factory()->create([
                'name' => 'Kegalle',
                'code' => 'KEG',
            ]);

        $this->zonalDirector =
            User::factory()->create([
                'assigned_zone_id' => $this->assignedZone->id,

                'email_verified_at' => now(),
            ]);

        $this->zonalDirector
            ->assignRole(
                'Zonal Director'
            );

        $this->principalUser =
            User::factory()->create([
                'email_verified_at' => now(),
            ]);

        $this->principalUser
            ->assignRole(
                'Principal'
            );
    }

    public function test_zonal_director_can_view_application_from_assigned_zone(): void
    {
        $application =
            $this->createApplicationForZone(
                $this->assignedZone,
                TransferApplication::STATUS_SUBMITTED
            );

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->get(
                route(
                    'zonal.transfer-applications.show',
                    $application
                )
            )
            ->assertOk();
    }

    public function test_zonal_director_cannot_view_application_from_other_zone(): void
    {
        $application =
            $this->createApplicationForZone(
                $this->otherZone,
                TransferApplication::STATUS_SUBMITTED
            );

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->get(
                route(
                    'zonal.transfer-applications.show',
                    $application
                )
            )
            ->assertForbidden();
    }

    public function test_super_admin_can_view_application_from_any_zone(): void
    {
        $superAdmin =
            User::factory()->create([
                'email_verified_at' => now(),
            ]);

        $superAdmin->assignRole(
            'Super Admin'
        );

        $application =
            $this->createApplicationForZone(
                $this->otherZone,
                TransferApplication::STATUS_SUBMITTED
            );

        $this
            ->actingAs(
                $superAdmin
            )
            ->get(
                route(
                    'zonal.transfer-applications.show',
                    $application
                )
            )
            ->assertOk();
    }

    public function test_submitted_application_can_enter_zonal_review(): void
    {
        Notification::fake();

        $application =
            $this->createApplicationForZone(
                $this->assignedZone,
                TransferApplication::STATUS_SUBMITTED
            );

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->post(
                route(
                    'zonal.transfer-applications.start-review',
                    $application
                )
            )
            ->assertRedirect();

        $this->assertDatabaseHas(
            'transfer_applications',
            [
                'id' => $application->id,

                'status' => TransferApplication::STATUS_ZONAL_REVIEW,
            ]
        );

        $this->assertDatabaseHas(
            'zonal_reviews',
            [
                'transfer_application_id' => $application->id,

                'zone_id' => $this->assignedZone->id,

                'reviewer_id' => $this->zonalDirector->id,
            ]
        );

        $this->assertDatabaseHas(
            'transfer_application_actions',
            [
                'transfer_application_id' => $application->id,

                'action' => TransferApplicationAction::ACTION_ZONAL_REVIEW_STARTED,

                'from_status' => TransferApplication::STATUS_SUBMITTED,

                'to_status' => TransferApplication::STATUS_ZONAL_REVIEW,

                'acted_by' => $this->zonalDirector->id,
            ]
        );

        Notification::assertSentTo(
            $this->principalUser,
            TransferApplicationZonalReviewStartedNotification::class
        );
    }

    public function test_invalid_status_cannot_enter_zonal_review(): void
    {
        $application =
            $this->createApplicationForZone(
                $this->assignedZone,
                TransferApplication::STATUS_ZONAL_APPROVED
            );

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->post(
                route(
                    'zonal.transfer-applications.start-review',
                    $application
                )
            )
            ->assertForbidden();
    }

    public function test_zonal_approval_records_decision_and_history(): void
    {
        Notification::fake();

        $application =
            $this->createApplicationForZone(
                $this->assignedZone,
                TransferApplication::STATUS_ZONAL_REVIEW
            );

        ZonalReview::factory()->create([
            'transfer_application_id' => $application->id,

            'zone_id' => $this->assignedZone->id,

            'reviewer_id' => $this->zonalDirector->id,

            'review_started_at' => now()->subHour(),
        ]);

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->post(
                route(
                    'zonal.transfer-applications.approve',
                    $application
                ),
                [
                    'recommendation' => 'Recommended',

                    'remarks' => 'The application meets Zonal requirements.',
                ]
            )
            ->assertRedirect();

        $this->assertDatabaseHas(
            'transfer_applications',
            [
                'id' => $application->id,

                'status' => TransferApplication::STATUS_ZONAL_APPROVED,
            ]
        );

        $this->assertDatabaseHas(
            'zonal_reviews',
            [
                'transfer_application_id' => $application->id,

                'reviewer_id' => $this->zonalDirector->id,

                'decision' => ZonalReview::DECISION_APPROVED,

                'recommendation' => 'Recommended',
            ]
        );

        $this->assertDatabaseHas(
            'transfer_application_actions',
            [
                'transfer_application_id' => $application->id,

                'action' => TransferApplicationAction::ACTION_ZONAL_APPROVED,

                'to_status' => TransferApplication::STATUS_ZONAL_APPROVED,
            ]
        );

        Notification::assertSentTo(
            $this->principalUser,
            ZonalDecisionRecordedNotification::class,
            function (
                ZonalDecisionRecordedNotification $notification
            ): bool {
                $data =
                    $notification->toDatabase(
                        $this->principalUser
                    );

                return data_get(
                    $data,
                    'metadata.decision'
                ) === 'approved'
                    && data_get(
                        $data,
                        'category'
                    ) === 'zonal_review';
            }
        );
    }

    public function test_rejection_reason_is_required(): void
    {
        $application =
            $this->createApplicationForZone(
                $this->assignedZone,
                TransferApplication::STATUS_ZONAL_REVIEW
            );

        ZonalReview::factory()->create([
            'transfer_application_id' => $application->id,

            'zone_id' => $this->assignedZone->id,

            'reviewer_id' => $this->zonalDirector->id,

            'review_started_at' => now()->subHour(),
        ]);

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->post(
                route(
                    'zonal.transfer-applications.reject',
                    $application
                ),
                [
                    'recommendation' => 'Not Recommended',

                    'rejection_reason' => '',
                ]
            )
            ->assertSessionHasErrors(
                'rejection_reason'
            );

        $this->assertDatabaseHas(
            'transfer_applications',
            [
                'id' => $application->id,

                'status' => TransferApplication::STATUS_ZONAL_REVIEW,
            ]
        );
    }

    public function test_zonal_rejection_records_decision_and_notification(): void
    {
        Notification::fake();

        $application =
            $this->createApplicationForZone(
                $this->assignedZone,
                TransferApplication::STATUS_ZONAL_REVIEW
            );

        ZonalReview::factory()->create([
            'transfer_application_id' => $application->id,

            'zone_id' => $this->assignedZone->id,

            'reviewer_id' => $this->zonalDirector->id,

            'review_started_at' => now()->subHour(),
        ]);

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->post(
                route(
                    'zonal.transfer-applications.reject',
                    $application
                ),
                [
                    'recommendation' => 'Not Recommended',

                    'remarks' => 'Reviewed by the Zone.',

                    'rejection_reason' => 'The minimum service requirement was not sufficiently supported.',
                ]
            )
            ->assertRedirect();

        $this->assertDatabaseHas(
            'transfer_applications',
            [
                'id' => $application->id,

                'status' => TransferApplication::STATUS_ZONAL_REJECTED,
            ]
        );

        $this->assertDatabaseHas(
            'zonal_reviews',
            [
                'transfer_application_id' => $application->id,

                'decision' => ZonalReview::DECISION_REJECTED,

                'reviewer_id' => $this->zonalDirector->id,
            ]
        );

        Notification::assertSentTo(
            $this->principalUser,
            ZonalDecisionRecordedNotification::class,
            function (
                ZonalDecisionRecordedNotification $notification
            ): bool {
                $data =
                    $notification->toDatabase(
                        $this->principalUser
                    );

                return data_get(
                    $data,
                    'metadata.decision'
                ) === 'rejected'
                    && data_get(
                        $data,
                        'category'
                    ) === 'zonal_review';
            }
        );
    }

    public function test_other_zone_cannot_start_review_using_direct_url(): void
    {
        $application =
            $this->createApplicationForZone(
                $this->otherZone,
                TransferApplication::STATUS_SUBMITTED
            );

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->post(
                route(
                    'zonal.transfer-applications.start-review',
                    $application
                )
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'transfer_applications',
            [
                'id' => $application->id,

                'status' => TransferApplication::STATUS_SUBMITTED,
            ]
        );
    }

    public function test_original_snapshot_remains_unchanged_after_review(): void
    {
        $application =
            $this->createApplicationForZone(
                $this->assignedZone,
                TransferApplication::STATUS_SUBMITTED
            );

        $originalName =
            $application->principal_name;

        $originalSchoolId =
            $application->current_school_id;

        $originalNic =
            $application->nic;

        $originalEmployeeNumber =
            $application->employee_number;

        $this
            ->actingAs(
                $this->zonalDirector
            )
            ->post(
                route(
                    'zonal.transfer-applications.start-review',
                    $application
                )
            )
            ->assertRedirect();

        $application->refresh();

        $this->assertSame(
            $originalName,
            $application->principal_name
        );

        $this->assertSame(
            $originalSchoolId,
            $application->current_school_id
        );

        $this->assertSame(
            $originalNic,
            $application->nic
        );

        $this->assertSame(
            $originalEmployeeNumber,
            $application->employee_number
        );
    }

    private function createApplicationForZone(
        Zone $zone,
        string $status
    ): TransferApplication {
        $division =
            Division::factory()->create([
                'zone_id' => $zone->id,
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

                'origin_zone_id' => $zone->id,

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
                    ->subYears(5)
                    ->toDateString(),

                'current_school_service_months' => 60,

                'transfer_reason' => 'Long Service',

                'reason_details' => 'Application created for Zonal workflow testing.',

                'has_medical_reason' => false,

                'has_spouse_employment_reason' => false,

                'is_mutual_transfer' => false,

                'mutual_principal_nic' => null,

                'principal_remarks' => null,

                'status' => $status,

                'submitted_at' => now(),

                'declaration_accepted' => true,
            ]);
    }
}
