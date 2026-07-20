<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use RuntimeException;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $ratnapuraZone = Zone::query()
            ->where('code', 'RAT')
            ->first();

        if (! $ratnapuraZone) {
            throw new RuntimeException(
                'Ratnapura Zone was not found. Run ZoneSeeder first.'
            );
        }

        $divisions = [
            [
                'zone_id' => $ratnapuraZone->id,
                'name' => 'Ratnapura',
                'code' => 'RAT-DIV-01',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'zone_id' => $ratnapuraZone->id,
                'name' => 'Kuruwita',
                'code' => 'RAT-DIV-02',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($divisions as $division) {
            Division::query()->updateOrCreate(
                [
                    'code' => $division['code'],
                ],
                $division
            );
        }
    }
}
