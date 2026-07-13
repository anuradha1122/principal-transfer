<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_super_admin_can_view_roles(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Super Admin');

        $response = $this
            ->actingAs($admin)
            ->get('/admin/roles');

        $response->assertOk();
    }

    public function test_principal_cannot_manage_roles(): void
    {
        $principal = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $principal->assignRole('Principal');

        $response = $this
            ->actingAs($principal)
            ->get('/admin/roles');

        $response->assertForbidden();
    }

    public function test_super_admin_can_create_role(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Super Admin');

        $response = $this
            ->actingAs($admin)
            ->post('/admin/roles', [
                'name' => 'Audit Officer',
                'permissions' => [
                    'view audit logs',
                    'view reports',
                ],
            ]);

        $response->assertRedirect('/admin/roles');

        $this->assertDatabaseHas('roles', [
            'name' => 'Audit Officer',
            'guard_name' => 'web',
        ]);
    }

    public function test_super_admin_role_cannot_be_deleted(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Super Admin');

        $role = $admin->roles()->firstOrFail();

        $response = $this
            ->actingAs($admin)
            ->delete("/admin/roles/{$role->id}");

        $response->assertSessionHas(
            'error',
            'System roles cannot be deleted.'
        );

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
        ]);
    }
}
