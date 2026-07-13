<?php

namespace Tests\Feature\Admin;

use App\Models\Division;
use App\Models\School;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationStructureTest extends TestCase
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

    public function test_super_admin_can_create_zone(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/zones', [
                'name' => 'Test Zone',
                'code' => 'TEST-Z',
                'district' => 'Ratnapura',
                'office_address' => null,
                'telephone' => null,
                'email' => null,
                'is_active' => true,
                'sort_order' => 1,
            ]);

        $response->assertRedirect('/admin/zones');

        $this->assertDatabaseHas('zones', [
            'name' => 'Test Zone',
            'code' => 'TEST-Z',
        ]);
    }

    public function test_principal_cannot_manage_zones(): void
    {
        $principal = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $principal->assignRole('Principal');

        $response = $this
            ->actingAs($principal)
            ->get('/admin/zones');

        $response->assertForbidden();
    }

    public function test_super_admin_can_create_division(): void
    {
        $zone = Zone::create([
            'name' => 'Ratnapura',
            'code' => 'RAT',
            'district' => 'Ratnapura',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/divisions', [
                'zone_id' => $zone->id,
                'name' => 'Ratnapura I',
                'code' => 'RAT-01',
                'office_address' => null,
                'telephone' => null,
                'email' => null,
                'is_active' => true,
                'sort_order' => 1,
            ]);

        $response->assertRedirect('/admin/divisions');

        $this->assertDatabaseHas('divisions', [
            'zone_id' => $zone->id,
            'name' => 'Ratnapura I',
        ]);
    }

    public function test_super_admin_can_create_school(): void
    {
        $zone = Zone::create([
            'name' => 'Ratnapura',
            'code' => 'RAT',
            'district' => 'Ratnapura',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $division = Division::create([
            'zone_id' => $zone->id,
            'name' => 'Ratnapura I',
            'code' => 'RAT-01',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post('/admin/schools', [
                'division_id' => $division->id,
                'census_number' => '12345',
                'name' => 'Test College',
                'school_type' => '1AB',
                'gender_type' => 'Mixed',
                'school_level' =>
                    'Primary and Secondary',
                'mediums' => [
                    'Sinhala',
                    'English',
                ],
                'address_line_1' => 'Main Road',
                'address_line_2' => null,
                'city' => 'Ratnapura',
                'postal_code' => null,
                'telephone' => null,
                'email' => null,
                'student_count' => 1000,
                'teacher_count' => 50,
                'is_national_school' => false,
                'is_active' => true,
            ]);

        $response->assertRedirect('/admin/schools');

        $this->assertDatabaseHas('schools', [
            'census_number' => '12345',
            'name' => 'Test College',
        ]);
    }

    public function test_zone_with_divisions_cannot_be_deleted(): void
    {
        $zone = Zone::create([
            'name' => 'Ratnapura',
            'code' => 'RAT',
            'district' => 'Ratnapura',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Division::create([
            'zone_id' => $zone->id,
            'name' => 'Ratnapura I',
            'code' => 'RAT-01',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete("/admin/zones/{$zone->id}");

        $response->assertSessionHas(
            'error',
            'This zone cannot be deleted because it contains divisions.'
        );

        $this->assertDatabaseHas('zones', [
            'id' => $zone->id,
        ]);
    }

    public function test_division_with_schools_cannot_be_deleted(): void
    {
        $zone = Zone::create([
            'name' => 'Ratnapura',
            'code' => 'RAT',
            'district' => 'Ratnapura',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $division = Division::create([
            'zone_id' => $zone->id,
            'name' => 'Ratnapura I',
            'code' => 'RAT-01',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        School::create([
            'division_id' => $division->id,
            'census_number' => '12345',
            'name' => 'Test School',
            'gender_type' => 'Mixed',
            'is_national_school' => false,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete(
                "/admin/divisions/{$division->id}"
            );

        $response->assertSessionHas(
            'error',
            'This division cannot be deleted because it contains schools.'
        );

        $this->assertDatabaseHas('divisions', [
            'id' => $division->id,
        ]);
    }
}
