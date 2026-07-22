<?php

namespace Tests\Feature\Reports;

use App\Models\Division;
use App\Models\PrincipalProfile;
use App\Models\School;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManagementReportTest extends TestCase
{
    use RefreshDatabase;

    private Zone $firstZone;

    private Zone $secondZone;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Super Admin');
        Role::findOrCreate('Principal');
        Role::findOrCreate('Zonal Director');
        Role::findOrCreate('Provincial Director');
        Role::findOrCreate(
            'Transfer Board Member'
        );

        $this->seed(
            RolePermissionSeeder::class
        );

        $this->firstZone =
            Zone::factory()->create([
                'name' => 'Ratnapura',
                'code' => 'RAT',
            ]);

        $this->secondZone =
            Zone::factory()->create([
                'name' => 'Kegalle',
                'code' => 'KEG',
            ]);
    }

    public function test_super_admin_can_view_management_reports(): void
    {
        $superAdmin =
            $this->createUserWithRole(
                'Super Admin'
            );

        $this->createTransferApplicationForZone(
            $this->firstZone,
            TransferApplication::STATUS_SUBMITTED
        );

        $this->createTransferApplicationForZone(
            $this->secondZone,
            TransferApplication::STATUS_ZONAL_APPROVED
        );

        $this
            ->actingAs($superAdmin)
            ->get(
                route('reports.index')
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page): Assert => $page
                    ->component(
                        'Reports/Index'
                    )
                    ->where(
                        'summary.total_applications',
                        2
                    )
                    ->has(
                        'zoneDistribution',
                        2
                    )
            );
    }

    public function test_zonal_director_only_sees_assigned_zone_data(): void
    {
        $zonalDirector =
            $this->createUserWithRole(
                'Zonal Director',
                [
                    'assigned_zone_id' => $this->firstZone->id,
                ]
            );

        $this->createTransferApplicationForZone(
            $this->firstZone,
            TransferApplication::STATUS_SUBMITTED
        );

        $this->createTransferApplicationForZone(
            $this->secondZone,
            TransferApplication::STATUS_SUBMITTED
        );

        $this
            ->actingAs($zonalDirector)
            ->get(
                route('reports.index')
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page): Assert => $page
                    ->where(
                        'summary.total_applications',
                        1
                    )
                    ->has(
                        'zoneDistribution',
                        1
                    )
                    ->where(
                        'zoneDistribution.0.zone_id',
                        $this->firstZone->id
                    )
            );
    }

    public function test_zonal_director_cannot_force_another_zone_using_query_parameter(): void
    {
        $zonalDirector =
            $this->createUserWithRole(
                'Zonal Director',
                [
                    'assigned_zone_id' => $this->firstZone->id,
                ]
            );

        $this->createTransferApplicationForZone(
            $this->firstZone,
            TransferApplication::STATUS_SUBMITTED
        );

        $this->createTransferApplicationForZone(
            $this->secondZone,
            TransferApplication::STATUS_SUBMITTED
        );

        $this
            ->actingAs($zonalDirector)
            ->get(
                route(
                    'reports.index',
                    [
                        'zone_id' => $this->secondZone->id,
                    ]
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page): Assert => $page
                    ->where(
                        'summary.total_applications',
                        1
                    )
                    ->where(
                        'zoneDistribution.0.zone_id',
                        $this->firstZone->id
                    )
            );
    }

    public function test_user_without_report_permission_is_forbidden(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this
            ->actingAs($user)
            ->get(
                route('reports.index')
            )
            ->assertForbidden();
    }

    public function test_status_filter_returns_matching_applications(): void
    {
        $superAdmin =
            $this->createUserWithRole(
                'Super Admin'
            );

        $this->createTransferApplicationForZone(
            $this->firstZone,
            TransferApplication::STATUS_SUBMITTED
        );

        $this->createTransferApplicationForZone(
            $this->firstZone,
            TransferApplication::STATUS_ZONAL_APPROVED
        );

        $this
            ->actingAs($superAdmin)
            ->get(
                route(
                    'reports.index',
                    [
                        'status' => TransferApplication::STATUS_SUBMITTED,
                    ]
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page): Assert => $page
                    ->where(
                        'summary.total_applications',
                        1
                    )
                    ->where(
                        'summary.submitted',
                        1
                    )
            );
    }

    private function createUserWithRole(
        string $role,
        array $attributes = []
    ): User {
        $user =
            User::factory()->create(
                array_merge(
                    [
                        'email_verified_at' => now(),

                        'is_active' => true,
                    ],
                    $attributes
                )
            );

        $user->assignRole($role);

        return $user;
    }

    private function createTransferApplicationForZone(
        Zone $zone,
        string $status
    ): TransferApplication {
        $principal =
            $this->createUserWithRole(
                'Principal'
            );

        $profile =
            PrincipalProfile::factory()
                ->create([
                    'user_id' => $principal->id,
                ]);

        $division =
            Division::factory()->create([
                'zone_id' => $zone->id,
            ]);

        $school =
            School::factory()->create([
                'division_id' => $division->id,
            ]);

        $cycle =
            TransferCycle::factory()
                ->create();

        return TransferApplication::factory()
            ->create([
                'principal_profile_id' => $profile->id,

                'transfer_cycle_id' => $cycle->id,

                'current_school_id' => $school->id,

                'origin_zone_id' => $zone->id,

                'principal_name' => $profile->full_name
                    ?? $principal->name,

                'nic' => $profile->nic
                    ?? '901234567V',

                'employee_number' => $profile->employee_number
                    ?? 'EMP0001',

                'current_designation' => 'Principal',

                'service_grade' => $profile->service_grade
                    ?? 'SLPS II',

                'current_appointment_start_date' => now()
                    ->subYears(8)
                    ->toDateString(),

                'current_school_service_months' => 96,

                'transfer_reason' => 'Long Service',

                'reason_details' => 'Created for management report testing.',

                'has_medical_reason' => false,

                'has_spouse_employment_reason' => false,

                'is_mutual_transfer' => false,

                'status' => $status,

                'submitted_at' => now(),

                'declaration_accepted' => true,
            ]);
    }
}
