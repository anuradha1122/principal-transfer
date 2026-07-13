<?php

namespace Tests\Feature\Authentication;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_super_admin_is_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Super Admin');

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_principal_is_redirected_to_principal_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Principal');

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertRedirect('/principal/dashboard');
    }

    public function test_principal_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Principal');

        $response = $this
            ->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Super Admin');

        $response = $this
            ->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertSuccessful();
    }
}
