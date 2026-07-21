<?php

namespace Database\Factories;

use App\Models\TransferApplication;
use App\Models\User;
use App\Models\ZonalReview;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ZonalReview>
 */
class ZonalReviewFactory extends Factory
{
    protected $model = ZonalReview::class;

    public function definition(): array
    {
        return [
            'transfer_application_id' => TransferApplication::factory(),

            'zone_id' => Zone::factory(),

            'reviewer_id' => User::factory(),

            'recommendation' => null,
            'decision' => null,
            'remarks' => null,
            'rejection_reason' => null,

            'review_started_at' => now(),
            'reviewed_at' => null,
        ];
    }
}
