<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'division_id' => Division::factory(),

            'census_number' => fake()
                ->unique()
                ->numerify('######'),

            'name' => fake()
                ->unique()
                ->company().' College',

            'school_type' => fake()->randomElement([
                '1AB',
                '1C',
                'Type 2',
                'Type 3',
                'Other',
            ]),

            'gender_type' => fake()->randomElement([
                'Mixed',
                'Boys',
                'Girls',
            ]),

            'school_level' => fake()->randomElement([
                'Primary',
                'Secondary',
                'Primary and Secondary',
            ]),

            'mediums' => fake()->randomElement([
                ['Sinhala'],
                ['Tamil'],
                ['English'],
                ['Sinhala', 'English'],
                ['Tamil', 'English'],
            ]),

            'address_line_1' => fake()->streetAddress(),

            'address_line_2' => fake()->optional()->secondaryAddress(),

            'city' => fake()->city(),

            'postal_code' => fake()->postcode(),

            'telephone' => fake()->numerify('0#########'),

            'email' => fake()
                ->unique()
                ->safeEmail(),

            'student_count' => fake()
                ->numberBetween(100, 2500),

            'teacher_count' => fake()
                ->numberBetween(10, 150),

            'is_national_school' => false,

            'is_active' => true,
        ];
    }
}
