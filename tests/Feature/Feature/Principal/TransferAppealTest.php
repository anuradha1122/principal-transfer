<?php

namespace Tests\Feature\Principal;

use App\Models\PrincipalProfile;
use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\TransferDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TransferAppealTest extends TestCase
{
    use RefreshDatabase;

    private User $principal;

    private PrincipalProfile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Principal']);

        $permissions = [
            'view own transfer appeals',
            'create transfer appeals',
            'edit draft transfer appeals',
            'submit transfer appeals',
            'withdraw transfer appeals',
            'upload transfer appeal documents',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role->syncPermissions($permissions);

        $this->principal = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->principal->assignRole($role);

        $this->profile = PrincipalProfile::factory()->create([
            'user_id' => $this->principal->id,
        ]);
    }

    public function test_principal_can_view_own_appeal_index(): void
    {
        $response = $this
            ->actingAs($this->principal)
            ->get(route('principal.transfer-appeals.index'));

        $response->assertOk();
    }

    public function test_principal_cannot_view_another_principals_appeal(): void
    {
        $otherProfile = PrincipalProfile::factory()->create();

        $appeal = TransferAppeal::factory()->create([
            'principal_profile_id' => $otherProfile->id,
        ]);

        $response = $this
            ->actingAs($this->principal)
            ->get(
                route(
                    'principal.transfer-appeals.show',
                    $appeal
                )
            );

        $response->assertForbidden();
    }

    public function test_only_draft_appeal_can_be_edited(): void
    {
        $appeal = TransferAppeal::factory()
            ->submitted()
            ->create([
                'principal_profile_id' => $this->profile->id,
            ]);

        $response = $this
            ->actingAs($this->principal)
            ->get(
                route(
                    'principal.transfer-appeals.edit',
                    $appeal
                )
            );

        $response->assertForbidden();
    }

    public function test_principal_can_submit_a_draft_appeal(): void
    {
        $application = TransferApplication::factory()->create([
            'principal_profile_id' => $this->profile->id,
            'status' => 'Approved',
        ]);

        TransferDocument::factory()->create([
            'transfer_application_id' => $application->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $appeal = TransferAppeal::factory()->create([
            'transfer_application_id' => $application->id,
            'principal_profile_id' => $this->profile->id,
            'status' => TransferAppeal::STATUS_DRAFT,
        ]);

        $response = $this
            ->actingAs($this->principal)
            ->post(
                route(
                    'principal.transfer-appeals.submit',
                    $appeal
                ),
                [
                    'declaration' => true,
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('transfer_appeals', [
            'id' => $appeal->id,
            'status' => TransferAppeal::STATUS_SUBMITTED,
        ]);

        $this->assertDatabaseHas('transfer_appeal_actions', [
            'transfer_appeal_id' => $appeal->id,
            'action' => 'Appeal Submitted',
        ]);
    }
}
