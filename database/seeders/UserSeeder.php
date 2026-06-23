<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Sistem',      'email' => 'admin@dashboard.com',   'password' => Hash::make('password')],
            ['name' => 'Budi Santoso',      'email' => 'budi@dashboard.com',    'password' => Hash::make('password')],
            ['name' => 'Siti Rahayu',       'email' => 'siti@dashboard.com',    'password' => Hash::make('password')],
            ['name' => 'Ahmad Fauzi',       'email' => 'ahmad@dashboard.com',   'password' => Hash::make('password')],
            ['name' => 'Dewi Kusuma',       'email' => 'dewi@dashboard.com',    'password' => Hash::make('password')],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}