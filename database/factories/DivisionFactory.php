<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Division>
 */
class DivisionFactory extends Factory
{
    protected $model = Division::class;

    public function definition(): array
    {
        return [
            'zone_id' => Zone::factory(),
            'name' => fake()->unique()->city().' Division',
            'code' => strtoupper(fake()->unique()->lexify('????')),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
