<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'risethunder7@gmail.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin@2026!'),
                'role'     => 'admin',
                'google_id' => null,
                'avatar'   => null,
            ]
        );

        $this->command->info('Admin account set: risethunder7@gmail.com (role: admin)');
    }
}
