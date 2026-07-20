<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Zone;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_super_admin_can_view_users(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Super Admin');

        $response = $this
            ->actingAs($admin)
            ->get('/admin/users');

        $response->assertOk();
    }

    public function test_principal_cannot_view_users(): void
    {
        $principal = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $principal->assignRole('Principal');

        $response = $this
            ->actingAs($principal)
            ->get('/admin/users');

        $response->assertForbidden();
    }

    public function test_super_admin_can_create_user(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $admin->assignRole('Super Admin');

        $zone = Zone::create([
            'name' => 'Ratnapura',
            'code' => 'RAT',
            'district' => 'Ratnapura',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Zonal Director',
                'email' => 'zonal@example.com',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
                'role' => 'Zonal Director',
                'assigned_zone_id' => $zone->id,
                'is_active' => true,
                'email_verified' => true,
            ]);

        $response->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'email' => 'zonal@example.com',
            'assigned_zone_id' => $zone->id,
            'is_active' => true,
        ]);

        $user = User::query()
            ->where('email', 'zonal@example.com')
            ->firstOrFail();

        $this->assertTrue(
            $user->hasRole('Zonal Director')
        );

        $this->assertSame(
            $zone->id,
            $user->assigned_zone_id
        );
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'password',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('email');
    }

    public function test_last_super_admin_cannot_be_deleted(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Super Admin');

        $response = $this
            ->actingAs($admin)
            ->delete("/admin/users/{$admin->id}");

        $response->assertSessionHas(
            'error',
            'You cannot delete your own account.'
        );

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }
}
