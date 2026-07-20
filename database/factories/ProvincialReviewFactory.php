<?php

namespace Database\Factories;

use App\Models\ProvincialReview;
use App\Models\TransferApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProvincialReviewFactory extends Factory
{
    protected $model = ProvincialReview::class;

    public function definition(): array
    {
        return [
            'transfer_application_id' =>
                TransferApplication::factory(),

            'reviewer_id' =>
                User::factory(),

            'decision' =>
                ProvincialReview::DECISION_PENDING,

            'recommendation' => null,

            'remarks' => null,

            'rejection_reason' => null,

            'return_reason' => null,

            'review_started_at' =>
                now()->subHour(),

            'reviewed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(
            fn (): array => [
                'decision' =>
                    ProvincialReview::DECISION_APPROVED,

                'recommendation' =>
                    'Recommended for Transfer Board consideration.',

                'reviewed_at' => now(),
            ]
        );
    }

    public function rejected(): static
    {
        return $this->state(
            fn (): array => [
                'decision' =>
                    ProvincialReview::DECISION_REJECTED,

                'rejection_reason' =>
                    'Application did not satisfy Provincial requirements.',

                'reviewed_at' => now(),
            ]
        );
    }
}
