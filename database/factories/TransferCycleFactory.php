<?php

namespace Database\Factories;

use App\Models\TransferCycle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferCycle>
 */
class TransferCycleFactory extends Factory
{
    protected $model = TransferCycle::class;

    public function definition(): array
    {
        $year = now()->year;

        return [
            'name' => "Annual Principal Transfer {$year}",

            'code' => fake()
                ->unique()
                ->bothify('TR-####'),

            'transfer_type' => fake()->randomElement([
                'Annual',
                'Special',
                'Mutual',
                'Administrative',
            ]),

            'transfer_year' => $year,

            'application_open_date' => now()
                ->subDays(5)
                ->toDateString(),

            'application_close_date' => now()
                ->addDays(20)
                ->toDateString(),

            'effective_from_date' => now()
                ->addMonths(3)
                ->toDateString(),

            'minimum_service_years' => 3,

            'maximum_preferences' => 3,

            'allow_same_zone_preferences' => true,

            'allow_other_zone_preferences' => true,

            'allow_withdrawal' => true,

            'status' => 'Published',

            'instructions' =>
                'Submit the application before the closing date.',

            'eligibility_notes' =>
                'Applicants must satisfy the minimum service requirement.',

            'created_by' => null,

            'updated_by' => null,

            'published_at' => now(),

            'closed_at' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(
            fn (): array => [
                'status' => 'Draft',
                'published_at' => null,
            ]
        );
    }

    public function published(): static
    {
        return $this->state(
            fn (): array => [
                'status' => 'Published',
                'published_at' => now(),
                'closed_at' => null,
            ]
        );
    }

    public function closed(): static
    {
        return $this->state(
            fn (): array => [
                'status' => 'Closed',
                'published_at' => now()
                    ->subMonth(),
                'closed_at' => now(),
                'application_close_date' => now()
                    ->subDay()
                    ->toDateString(),
            ]
        );
    }
}
