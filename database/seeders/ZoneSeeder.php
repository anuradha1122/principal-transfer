<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Ratnapura',
                'code' => 'RAT',
                'district' => 'Ratnapura',
                'sort_order' => 1,
            ],
            [
                'name' => 'Balangoda',
                'code' => 'BAL',
                'district' => 'Ratnapura',
                'sort_order' => 2,
            ],
            [
                'name' => 'Embilipitiya',
                'code' => 'EMB',
                'district' => 'Ratnapura',
                'sort_order' => 3,
            ],
            [
                'name' => 'Nivithigala',
                'code' => 'NIV',
                'district' => 'Ratnapura',
                'sort_order' => 4,
            ],
            [
                'name' => 'Kegalle',
                'code' => 'KEG',
                'district' => 'Kegalle',
                'sort_order' => 5,
            ],
            [
                'name' => 'Mawanella',
                'code' => 'MAW',
                'district' => 'Kegalle',
                'sort_order' => 6,
            ],
            [
                'name' => 'Dehiowita',
                'code' => 'DEH',
                'district' => 'Kegalle',
                'sort_order' => 7,
            ],
        ];

        foreach ($zones as $zone) {
            Zone::updateOrCreate(
                [
                    'code' => $zone['code'],
                ],
                [
                    ...$zone,
                    'is_active' => true,
                ]
            );
        }
    }
}
