<?php

namespace Tests\Feature\TransferBoard;

use App\Models\PrincipalProfile;
use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TransferAppealReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $reviewer;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate([
            'name' => 'Transfer Board Member',
        ]);

        $permissions = [
            'view transfer appeals',
            'review transfer appeals',
            'approve transfer appeals',
            'reject transfer appeals',
            'return transfer appeals',
            'download transfer appeal documents',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role->syncPermissions($permissions);

        $this->reviewer = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->reviewer->assignRole($role);
    }

    public function test_board_member_can_start_appeal_review(): void
    {
        $appeal = $this->createAppeal(
            TransferAppeal::STATUS_SUBMITTED
        );

        $response = $this
            ->actingAs($this->reviewer)
            ->post(
                route(
                    'transfer-board.transfer-appeals.start-review',
                    $appeal
                )
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('transfer_appeals', [
            'id' => $appeal->id,
            'status' => TransferAppeal::STATUS_UNDER_REVIEW,
            'reviewer_id' => $this->reviewer->id,
        ]);
    }

    public function test_board_member_can_return_appeal_for_clarification(): void
    {
        $appeal = $this->createAppeal(
            TransferAppeal::STATUS_UNDER_REVIEW
        );

        $response = $this
            ->actingAs($this->reviewer)
            ->post(
                route(
                    'transfer-board.transfer-appeals.return',
                    $appeal
                ),
                [
                    'clarification_request' => 'Please provide documentary proof of the stated medical circumstances.',
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('transfer_appeals', [
            'id' => $appeal->id,
            'status' => TransferAppeal::STATUS_RETURNED,
        ]);
    }

    public function test_board_member_can_reject_appeal(): void
    {
        $appeal = $this->createAppeal(
            TransferAppeal::STATUS_UNDER_REVIEW
        );

        $response = $this
            ->actingAs($this->reviewer)
            ->post(
                route(
                    'transfer-board.transfer-appeals.reject',
                    $appeal
                ),
                [
                    'rejection_reason' => 'The appeal does not provide sufficient grounds to revise the original decision.',
                    'decision_remarks' => 'The original Transfer Board decision remains valid.',
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('transfer_appeals', [
            'id' => $appeal->id,
            'status' => TransferAppeal::STATUS_REJECTED,
            'decision_outcome' => TransferAppeal::DECISION_REJECTED,
        ]);
    }

    private function createAppeal(string $status): TransferAppeal
    {
        $profile = PrincipalProfile::factory()->create();

        $application = TransferApplication::factory()->create([
            'principal_profile_id' => $profile->id,
            'status' => 'Approved',
        ]);

        return TransferAppeal::factory()->create([
            'transfer_application_id' => $application->id,
            'principal_profile_id' => $profile->id,
            'status' => $status,
            'submitted_at' => now()->subDay(),
            'review_started_at' => $status === TransferAppeal::STATUS_UNDER_REVIEW
                    ? now()
                    : null,
        ]);
    }
}
