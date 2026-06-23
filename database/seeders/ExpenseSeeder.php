<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $expenses = [
            [
                'id' => 1,
                'project' => 'Project Title 1',
                'date' => '2018-04-10',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 300,
            ],
            [
                'id' => 2,
                'project' => 'Project Title 2',
                'date' => '2018-04-14',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 200,
            ],
            [
                'id' => 3,
                'project' => 'Project Title 3',
                'date' => '2018-04-15',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 800,
            ],
            [
                'id' => 4,
                'project' => 'Project Title 4',
                'date' => '2018-04-10',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 400,
            ],
            [
                'id' => 5,
                'project' => 'Project Title 5',
                'date' => '2018-04-17',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 300,
            ],
            [
                'id' => 6,
                'project' => 'Project Title 6',
                'date' => '2018-04-18',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 400,
            ],
            [
                'id' => 7,
                'project' => 'Project Title 7',
                'date' => '2018-04-19',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 500,
            ],
            [
                'id' => 8,
                'project' => 'Project Title 8',
                'date' => '2018-04-20',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 1600,
            ],
            [
                'id' => 9,
                'project' => 'Project Title 9',
                'date' => '2018-04-21',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 800,
            ],
            [
                'id' => 10,
                'project' => 'Project Title 10',
                'date' => '2018-04-22',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 100,
            ],
            [
                'id' => 11,
                'project' => 'Project Title 11',
                'date' => '2018-04-23',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 50,
            ],
            [
                'id' => 12,
                'project' => 'Project Title 12',
                'date' => '2018-04-24',
                'merchant' => null,
                'po_number' => null,
                'ticket_number' => null,
                'amount' => 100,
            ],
        ];

        foreach ($expenses as $expense) {
            DB::table('expenses')->updateOrInsert(['id' => $expense['id']], $expense);
        }
    }
}
