<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = [
            [
                'id' => 1,
                'invoice_number' => 'I000112',
                'client' => 'Customer Name 1',
                'project' => 'Project Title 1',
                'date_created' => '2018-04-11',
                'amount' => 400,
                'paid' => 200,
                'due' => 200,
            ],
            [
                'id' => 2,
                'invoice_number' => 'I000113',
                'client' => 'Customer Name 2',
                'project' => 'Project Title 2',
                'date_created' => '2018-04-12',
                'amount' => 400,
                'paid' => 200,
                'due' => 200,
            ],
            [
                'id' => 3,
                'invoice_number' => 'I000114',
                'client' => 'Customer Name 3',
                'project' => 'Project Title 3',
                'date_created' => '2018-04-13',
                'amount' => 600,
                'paid' => 200,
                'due' => 400,
            ],
            [
                'id' => 4,
                'invoice_number' => 'I000115',
                'client' => 'Customer Name 4',
                'project' => 'Project Title 4',
                'date_created' => '2018-04-14',
                'amount' => 500,
                'paid' => 200,
                'due' => 300,
            ],
            [
                'id' => 5,
                'invoice_number' => 'I000116',
                'client' => 'Customer Name 5',
                'project' => 'Project Title 5',
                'date_created' => '2018-04-15',
                'amount' => 500,
                'paid' => 200,
                'due' => 300,
            ],
            [
                'id' => 6,
                'invoice_number' => 'I000117',
                'client' => 'Customer Name 6',
                'project' => 'Project Title 6',
                'date_created' => '2018-04-16',
                'amount' => 200,
                'paid' => 200,
                'due' => 0,
            ],
            [
                'id' => 7,
                'invoice_number' => 'I000118',
                'client' => 'Customer Name 7',
                'project' => 'Project Title 7',
                'date_created' => '2018-04-17',
                'amount' => 400,
                'paid' => 200,
                'due' => 200,
            ],
            [
                'id' => 8,
                'invoice_number' => 'I000119',
                'client' => 'Customer Name 8',
                'project' => 'Project Title 8',
                'date_created' => '2018-04-18',
                'amount' => 500,
                'paid' => 200,
                'due' => 300,
            ],
            [
                'id' => 9,
                'invoice_number' => 'I000120',
                'client' => 'Customer Name 9',
                'project' => 'Project Title 9',
                'date_created' => '2018-04-19',
                'amount' => 300,
                'paid' => 200,
                'due' => 100,
            ],
        ];

        foreach ($invoices as $invoice) {
            DB::table('invoices')->updateOrInsert(['id' => $invoice['id']], $invoice);
        }
    }
}
