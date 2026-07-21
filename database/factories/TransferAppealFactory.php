<?php

namespace Database\Factories;

use App\Models\PrincipalProfile;
use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferAppeal>
 */
class TransferAppealFactory extends Factory
{
    protected $model = TransferAppeal::class;

    public function definition(): array
    {
        return [
            'transfer_application_id' => TransferApplication::factory(),
            'principal_profile_id' => PrincipalProfile::factory(),
            'appeal_number' => 'APL-'.
                now()->format('Y').
                '-'.
                fake()->unique()->numerify('######'),
            'appeal_reason' => fake()->sentence(),
            'appeal_details' => fake()->paragraphs(3, true),
            'requested_outcome' => fake()->paragraph(),
            'status' => TransferAppeal::STATUS_DRAFT,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status' => TransferAppeal::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function underReview(): static
    {
        return $this->state(fn () => [
            'status' => TransferAppeal::STATUS_UNDER_REVIEW,
            'submitted_at' => now()->subDay(),
            'review_started_at' => now(),
            'reviewer_id' => User::factory(),
        ]);
    }
}
