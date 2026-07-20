<?php

namespace Tests\Feature\Principal;

use App\Models\Division;
use App\Models\PrincipalAppointment;
use App\Models\PrincipalProfile;
use App\Models\School;
use App\Models\TransferCycle;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferApplicationManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $principal;

    private PrincipalProfile $profile;

    private School $currentSchool;

    private School $preferredSchool;

    private TransferCycle $cycle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(
            RolePermissionSeeder::class
        );

        $this->principal =
            User::factory()->create([
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

        $this->principal->assignRole(
            'Principal'
        );

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

        $this->currentSchool =
            School::create([
                'division_id' => $division->id,
                'census_number' => '10001',
                'name' => 'Current School',
                'gender_type' => 'Mixed',
                'is_active' => true,
            ]);

        $this->preferredSchool =
            School::create([
                'division_id' => $division->id,
                'census_number' => '10002',
                'name' => 'Preferred School',
                'gender_type' => 'Mixed',
                'is_active' => true,
            ]);

        $this->profile =
            PrincipalProfile::create([
                'user_id' => $this->principal->id,
                'nic' => '123456789V',
                'full_name' => 'Test Principal',
                'service_category' => 'Sri Lanka Principals Service',
                'employment_status' => 'Active',
            ]);

        PrincipalAppointment::create([
            'principal_profile_id' => $this->profile->id,
            'school_id' => $this->currentSchool->id,
            'designation' => 'Principal',
            'appointment_type' => 'Permanent',
            'appointment_date' => now()
                ->subYears(5)
                ->toDateString(),
            'start_date' => now()
                ->subYears(5)
                ->toDateString(),
            'is_current' => true,
        ]);

        $this->cycle =
            TransferCycle::create([
                'name' => 'Annual Transfer 2027',
                'code' => 'AT-2027',
                'transfer_type' => 'Annual',
                'transfer_year' => 2027,
                'application_open_date' => today()
                    ->subDay()
                    ->toDateString(),
                'application_close_date' => today()
                    ->addMonth()
                    ->toDateString(),
                'effective_from_date' => today()
                    ->addMonths(6)
                    ->toDateString(),
                'minimum_service_years' => 3,
                'maximum_preferences' => 3,
                'allow_same_zone_preferences' => true,
                'allow_other_zone_preferences' => true,
                'allow_withdrawal' => true,
                'status' => 'Published',
            ]);
    }

    public function test_principal_can_create_draft_application(): void
    {
        $response = $this
            ->actingAs($this->principal)
            ->post(
                '/principal/transfer-applications',
                [
                    'transfer_cycle_id' => $this->cycle->id,
                    'transfer_reason' => 'Long Service',
                    'reason_details' => 'I have completed more than five years of service at my current school.',
                    'has_medical_reason' => false,
                    'has_spouse_employment_reason' => false,
                    'is_mutual_transfer' => false,
                    'mutual_principal_nic' => null,
                    'principal_remarks' => null,
                    'preferences' => [
                        [
                            'school_id' => $this
                                ->preferredSchool
                                ->id,
                            'preference_reason' => 'Closer to residence.',
                        ],
                    ],
                ]
            );

        $application =
            $this->profile
                ->transferApplications()
                ->firstOrFail();

        $response->assertRedirect(
            "/principal/transfer-applications/{$application->id}"
        );

        $this->assertSame(
            'Draft',
            $application->status
        );

        $this->assertDatabaseHas(
            'transfer_preferences',
            [
                'transfer_application_id' => $application->id,
                'school_id' => $this
                    ->preferredSchool
                    ->id,
                'preference_order' => 1,
            ]
        );
    }

    public function test_principal_cannot_select_current_school_as_preference(): void
    {
        $response = $this
            ->actingAs($this->principal)
            ->post(
                '/principal/transfer-applications',
                [
                    'transfer_cycle_id' => $this->cycle->id,
                    'transfer_reason' => 'Long Service',
                    'reason_details' => 'I have completed sufficient service at this school.',
                    'has_medical_reason' => false,
                    'has_spouse_employment_reason' => false,
                    'is_mutual_transfer' => false,
                    'mutual_principal_nic' => null,
                    'principal_remarks' => null,
                    'preferences' => [
                        [
                            'school_id' => $this
                                ->currentSchool
                                ->id,
                            'preference_reason' => null,
                        ],
                    ],
                ]
            );

        $response->assertSessionHasErrors(
            'preferences.0.school_id'
        );
    }
}
