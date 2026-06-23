<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'id' => 1,
                'name' => 'Customer Name 1',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 2,
                'name' => 'Customer Name 2',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 3,
                'name' => 'Customer Name 3',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 4,
                'name' => 'Customer Name 4',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 5,
                'name' => 'Customer Name 5',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 6,
                'name' => 'Customer Name 6',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 7,
                'name' => 'Customer Name 7',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 8,
                'name' => 'Customer Name 8',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 9,
                'name' => 'Customer Name 9',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
            [
                'id' => 10,
                'name' => 'Customer Name 10',
                'company' => null,
                'country' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
            ],
        ];

        foreach ($clients as $client) {
            DB::table('clients')->updateOrInsert(['id' => $client['id']], $client);
        }
    }
}
