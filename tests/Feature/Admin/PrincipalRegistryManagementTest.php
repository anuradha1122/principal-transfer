<?php

namespace Tests\Feature\Admin;

use App\Models\PrincipalRegistry;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrincipalRegistryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->admin->assignRole('Super Admin');
    }

    public function test_super_admin_can_view_registry(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/principal-registry');

        $response->assertOk();
    }

    public function test_principal_cannot_view_registry(): void
    {
        $principal = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $principal->assignRole('Principal');

        $response = $this
            ->actingAs($principal)
            ->get('/admin/principal-registry');

        $response->assertForbidden();
    }

    public function test_super_admin_can_create_registry_record(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                '/admin/principal-registry',
                [
                    'nic' => '123456789V',
                    'full_name' => 'Test Principal',
                    'name_with_initials' => 'T Principal',
                    'school_id' => null,
                    'designation' => 'Principal',
                    'employee_number' => 'EMP001',
                    'is_active' => true,
                    'notes' => null,
                ]
            );

        $response->assertRedirect(
            '/admin/principal-registry'
        );

        $this->assertDatabaseHas(
            'principal_registries',
            [
                'normalized_nic' => '123456789V',
                'registration_status' => 'unregistered',
            ]
        );
    }

    public function test_duplicate_normalized_nic_is_rejected(): void
    {
        PrincipalRegistry::create([
            'nic' => '123456789V',
            'normalized_nic' => '123456789V',
            'registration_status' => 'unregistered',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(
                '/admin/principal-registry',
                [
                    'nic' => '123 456 789 v',
                    'full_name' => null,
                    'name_with_initials' => null,
                    'school_id' => null,
                    'designation' => null,
                    'employee_number' => null,
                    'is_active' => true,
                    'notes' => null,
                ]
            );

        $response->assertSessionHasErrors(
            'nic'
        );

        $this->assertSame(
            1,
            PrincipalRegistry::count()
        );
    }

    public function test_registered_record_cannot_be_deleted(): void
    {
        $principal = User::factory()->create();

        $principal->assignRole('Principal');

        $registry = PrincipalRegistry::create([
            'nic' => '123456789V',
            'normalized_nic' => '123456789V',
            'registration_status' => 'registered',
            'registered_user_id' => $principal->id,
            'registered_at' => now(),
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete(
                "/admin/principal-registry/{$registry->id}"
            );

        $response->assertSessionHas(
            'error'
        );

        $this->assertDatabaseHas(
            'principal_registries',
            [
                'id' => $registry->id,
            ]
        );
    }
}
