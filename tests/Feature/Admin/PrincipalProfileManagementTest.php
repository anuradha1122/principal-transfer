<?php

namespace Tests\Feature\Admin;

use App\Models\Division;
use App\Models\PrincipalAppointment;
use App\Models\PrincipalProfile;
use App\Models\School;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrincipalProfileManagementTest extends TestCase
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

        $this->admin->assignRole(
            'Super Admin'
        );
    }

    public function test_super_admin_can_view_profiles(): void
    {
        $this
            ->actingAs($this->admin)
            ->get('/admin/principal-profiles')
            ->assertOk();
    }

    public function test_principal_cannot_access_admin_profiles(): void
    {
        $principal = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $principal->assignRole(
            'Principal'
        );

        $this
            ->actingAs($principal)
            ->get('/admin/principal-profiles')
            ->assertForbidden();
    }

    public function test_super_admin_can_create_profile(): void
    {
        $principal = User::factory()->create();

        $principal->assignRole(
            'Principal'
        );

        $response = $this
            ->actingAs($this->admin)
            ->post(
                '/admin/principal-profiles',
                [
                    'user_id' => $principal->id,
                    'principal_registry_id' => null,
                    'nic' => '123456789V',
                    'employee_number' => 'EMP001',
                    'full_name' => 'Test Principal',
                    'name_with_initials' => 'T Principal',
                    'gender' => 'Male',
                    'date_of_birth' => '1980-01-01',
                    'mobile_number' => '0711234567',
                    'alternate_number' => null,
                    'personal_email' => 'personal@example.com',
                    'address_line_1' => 'Main Road',
                    'address_line_2' => null,
                    'city' => 'Ratnapura',
                    'postal_code' => null,
                    'service_category' => 'Sri Lanka Principals Service',
                    'service_grade' => 'Grade II',
                    'first_appointment_date' => '2005-01-01',
                    'principal_service_entry_date' => '2015-01-01',
                    'retirement_date' => '2040-01-01',
                    'employment_status' => 'Active',
                    'qualifications_summary' => null,
                    'notes' => null,
                    'profile_completed' => true,
                ]
            );

        $response->assertRedirect(
            '/admin/principal-profiles'
        );

        $this->assertDatabaseHas(
            'principal_profiles',
            [
                'user_id' => $principal->id,
                'nic' => '123456789V',
            ]
        );
    }

    public function test_new_current_appointment_closes_previous_current_appointment(): void
    {
        $principal = User::factory()->create();

        $principal->assignRole(
            'Principal'
        );

        $profile = PrincipalProfile::create([
            'user_id' => $principal->id,
            'nic' => '123456789V',
            'full_name' => 'Test Principal',
            'service_category' => 'Sri Lanka Principals Service',
            'employment_status' => 'Active',
        ]);

        $zone = Zone::create([
            'name' => 'Ratnapura',
            'code' => 'RAT',
            'district' => 'Ratnapura',
            'is_active' => true,
        ]);

        $division = Division::create([
            'zone_id' => $zone->id,
            'name' => 'Ratnapura',
            'code' => 'RAT-01',
            'is_active' => true,
        ]);

        $schoolOne = School::create([
            'division_id' => $division->id,
            'census_number' => '10001',
            'name' => 'School One',
            'gender_type' => 'Mixed',
            'is_active' => true,
        ]);

        $schoolTwo = School::create([
            'division_id' => $division->id,
            'census_number' => '10002',
            'name' => 'School Two',
            'gender_type' => 'Mixed',
            'is_active' => true,
        ]);

        $oldAppointment = PrincipalAppointment::create([
            'principal_profile_id' => $profile->id,
            'school_id' => $schoolOne->id,
            'designation' => 'Principal',
            'appointment_type' => 'Permanent',
            'appointment_date' => '2020-01-01',
            'start_date' => '2020-01-01',
            'is_current' => true,
        ]);

        $this
            ->actingAs($this->admin)
            ->post(
                "/admin/principal-profiles/{$profile->id}/appointments",
                [
                    'school_id' => $schoolTwo->id,
                    'designation' => 'Principal',
                    'appointment_type' => 'Permanent',
                    'appointment_number' => 'APT-002',

                    /*
                     * The request requires start_date to match
                     * appointment_date.
                     */
                    'appointment_date' => '2026-01-10',
                    'start_date' => '2026-01-10',

                    'end_date' => null,
                    'is_current' => true,
                    'reason_for_end' => null,
                    'remarks' => null,
                ]
            )
            ->assertRedirect(
                "/admin/principal-profiles/{$profile->id}"
            );

        $oldAppointment->refresh();

        $this->assertFalse(
            $oldAppointment->is_current
        );

        $this->assertSame(
            '2026-01-09',
            $oldAppointment
                ->end_date
                ->toDateString()
        );

        $this->assertSame(
            'Superseded by a new appointment',
            $oldAppointment->reason_for_end
        );

        $this->assertDatabaseHas(
            'principal_appointments',
            [
                'principal_profile_id' => $profile->id,
                'school_id' => $schoolTwo->id,
                'appointment_date' => '2026-01-10 00:00:00',
                'start_date' => '2026-01-10 00:00:00',
                'is_current' => 1,
            ]
        );
    }
}
