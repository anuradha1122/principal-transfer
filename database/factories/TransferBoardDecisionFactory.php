<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\TransferApplication;
use App\Models\TransferBoardDecision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferBoardDecisionFactory extends Factory
{
    protected $model =
        TransferBoardDecision::class;

    public function definition(): array
    {
        return [
            'transfer_application_id' =>
                TransferApplication::factory(),

            'reviewer_id' =>
                User::factory(),

            'decision' =>
                TransferBoardDecision::DECISION_PENDING,

            'recommended_school_id' => null,

            'effective_date' => null,

            'appointment_type' => null,

            'decision_reference' => null,

            'remarks' => null,

            'rejection_reason' => null,

            'waitlist_reason' => null,

            'review_started_at' =>
                now()->subHour(),

            'decided_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(
            fn (): array => [
                'decision' =>
                    TransferBoardDecision::DECISION_APPROVED,

                'recommended_school_id' =>
                    School::factory(),

                'effective_date' =>
                    now()->addMonth()->toDateString(),

                'appointment_type' =>
                    'Permanent',

                'decision_reference' =>
                    fake()->unique()->bothify(
                        'TBD-####'
                    ),

                'decided_at' => now(),
            ]
        );
    }

    public function rejected(): static
    {
        return $this->state(
            fn (): array => [
                'decision' =>
                    TransferBoardDecision::DECISION_REJECTED,

                'decision_reference' =>
                    fake()->unique()->bothify(
                        'TBD-####'
                    ),

                'rejection_reason' =>
                    'The application was not approved by the Transfer Board.',

                'decided_at' => now(),
            ]
        );
    }

    public function waitlisted(): static
    {
        return $this->state(
            fn (): array => [
                'decision' =>
                    TransferBoardDecision::DECISION_WAITLISTED,

                'decision_reference' =>
                    fake()->unique()->bothify(
                        'TBD-####'
                    ),

                'waitlist_reason' =>
                    'No suitable vacancy is currently available.',

                'decided_at' => now(),
            ]
        );
    }
}
