<?php

namespace Tests\Feature\Admin;

use App\Models\TransferCycle;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferCycleManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(
            RolePermissionSeeder::class
        );

        $this->admin =
            User::factory()->create([
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

        $this->admin->assignRole(
            'Super Admin'
        );
    }

    public function test_super_admin_can_view_transfer_cycles(): void
    {
        $this
            ->actingAs($this->admin)
            ->get('/admin/transfer-cycles')
            ->assertOk();
    }

    public function test_super_admin_can_create_transfer_cycle(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                '/admin/transfer-cycles',
                [
                    'name' => 'Annual Transfer 2027',
                    'code' => 'AT-2027',
                    'transfer_type' => 'Annual',
                    'transfer_year' => 2027,
                    'application_open_date' => '2026-08-01',
                    'application_close_date' => '2026-08-31',
                    'effective_from_date' => '2027-01-01',
                    'minimum_service_years' => 3,
                    'maximum_preferences' => 3,
                    'allow_same_zone_preferences' => true,
                    'allow_other_zone_preferences' => true,
                    'allow_withdrawal' => true,
                    'status' => 'Published',
                    'instructions' => null,
                    'eligibility_notes' => null,
                ]
            );

        $cycle = TransferCycle::firstOrFail();

        $response->assertRedirect(
            "/admin/transfer-cycles/{$cycle->id}"
        );

        $this->assertDatabaseHas(
            'transfer_cycles',
            [
                'code' => 'AT-2027',
                'status' => 'Published',
            ]
        );
    }
}
