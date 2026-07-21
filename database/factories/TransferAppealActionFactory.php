<?php

namespace Database\Factories;

use App\Models\TransferAppeal;
use App\Models\TransferAppealAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferAppealAction>
 */
class TransferAppealActionFactory extends Factory
{
    protected $model = TransferAppealAction::class;

    public function definition(): array
    {
        return [
            'transfer_appeal_id' => TransferAppeal::factory(),
            'action' => 'Appeal Submitted',
            'from_status' => TransferAppeal::STATUS_DRAFT,
            'to_status' => TransferAppeal::STATUS_SUBMITTED,
            'remarks' => null,
            'acted_by' => User::factory(),
            'acted_at' => now(),
            'metadata' => null,
        ];
    }
}
