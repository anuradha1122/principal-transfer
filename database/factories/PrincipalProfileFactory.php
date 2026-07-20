<?php

namespace Database\Factories;

use App\Models\PrincipalProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrincipalProfile>
 */
class PrincipalProfileFactory extends Factory
{
    protected $model = PrincipalProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'nic' => fake()->unique()->numerify('#########V'),
            'employee_number' => fake()->unique()->numerify('EMP#####'),
            'service_grade' => fake()->randomElement([
                'SLPS I',
                'SLPS II',
                'SLPS III',
            ]),
            'employment_status' => 'Active',
        ];
    }
}
