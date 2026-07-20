<?php

namespace Database\Factories;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Zone>
 */
class ZoneFactory extends Factory
{
    protected $model = Zone::class;

    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'name' => $name,
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'district' => fake()->randomElement([
                'Ratnapura',
                'Kegalle',
            ]),
            'office_address' => fake()->address(),
            'telephone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
