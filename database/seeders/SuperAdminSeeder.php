<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the initial Super Admin account.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'admin@principaltransfer.lk',
            ],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@123456'),
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles(['Super Admin']);
    }
}
