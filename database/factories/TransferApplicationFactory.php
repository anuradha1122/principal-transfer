<?php

namespace Database\Factories;

use App\Models\PrincipalProfile;
use App\Models\School;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferApplication>
 */
class TransferApplicationFactory extends Factory
{
    protected $model = TransferApplication::class;

    public function definition(): array
    {
        return [
            'transfer_cycle_id' => TransferCycle::factory(),
            'principal_profile_id' => PrincipalProfile::factory(),
            'current_school_id' => School::factory(),

            'application_number' => fake()->unique()->bothify('TR-######'),

            'principal_name' => fake()->name(),
            'nic' => fake()->unique()->numerify('#########V'),
            'employee_number' => fake()->unique()->numerify('EMP#####'),

            'current_designation' => 'Principal',
            'service_grade' => 'SLPS II',
            'current_appointment_start_date' => now()
                ->subYears(5)
                ->toDateString(),
            'current_school_service_months' => 60,

            'transfer_reason' => 'Long Service',
            'reason_details' => fake()->sentence(),
            'has_medical_reason' => false,
            'has_spouse_employment_reason' => false,
            'is_mutual_transfer' => false,
            'mutual_principal_nic' => null,
            'principal_remarks' => null,

            'status' => TransferApplication::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'declaration_accepted' => true,
        ];
    }
}
