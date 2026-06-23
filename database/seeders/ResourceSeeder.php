<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            [
                'id' => 1,
                'name' => 'Person Name 1',
                'type' => 'Full Time',
                'department' => 'Network',
                'rate_per_hour' => 200,
                'max_hours_per_day' => 8,
            ],
            [
                'id' => 2,
                'name' => 'Person Name 2',
                'type' => 'Part Time',
                'department' => 'Coding',
                'rate_per_hour' => 300,
                'max_hours_per_day' => 8,
            ],
            [
                'id' => 3,
                'name' => 'Person Name 3',
                'type' => 'Full Time',
                'department' => 'Testing',
                'rate_per_hour' => 500,
                'max_hours_per_day' => 10,
            ],
            [
                'id' => 4,
                'name' => 'Person Name 4',
                'type' => 'Full Time',
                'department' => 'Network',
                'rate_per_hour' => 400,
                'max_hours_per_day' => 12,
            ],
            [
                'id' => 5,
                'name' => 'Person Name 5',
                'type' => 'Full Time',
                'department' => 'Network',
                'rate_per_hour' => 200,
                'max_hours_per_day' => 5,
            ],
            [
                'id' => 6,
                'name' => 'Person Name 6',
                'type' => 'Part Time',
                'department' => 'Coding',
                'rate_per_hour' => 300,
                'max_hours_per_day' => 8,
            ],
            [
                'id' => 7,
                'name' => 'Person Name 7',
                'type' => 'Full Time',
                'department' => 'Testing',
                'rate_per_hour' => 500,
                'max_hours_per_day' => 10,
            ],
            [
                'id' => 8,
                'name' => 'Person Name 8',
                'type' => 'Full Time',
                'department' => 'Network',
                'rate_per_hour' => 400,
                'max_hours_per_day' => 12,
            ],
            [
                'id' => 9,
                'name' => 'Person Name 9',
                'type' => 'Full Time',
                'department' => 'Network',
                'rate_per_hour' => 200,
                'max_hours_per_day' => 5,
            ],
            [
                'id' => 10,
                'name' => 'Person Name 10',
                'type' => 'Part Time',
                'department' => 'Coding',
                'rate_per_hour' => 300,
                'max_hours_per_day' => 8,
            ],
            [
                'id' => 11,
                'name' => 'Person Name 11',
                'type' => 'Full Time',
                'department' => 'Testing',
                'rate_per_hour' => 500,
                'max_hours_per_day' => 10,
            ],
            [
                'id' => 12,
                'name' => 'Person Name 12',
                'type' => 'Full Time',
                'department' => 'Network',
                'rate_per_hour' => 400,
                'max_hours_per_day' => 12,
            ],
            [
                'id' => 13,
                'name' => 'Person Name 13',
                'type' => 'Full Time',
                'department' => 'Testing',
                'rate_per_hour' => 300,
                'max_hours_per_day' => 8,
            ],
            [
                'id' => 14,
                'name' => 'Person Name 14',
                'type' => 'Full Time',
                'department' => 'Coding',
                'rate_per_hour' => 300,
                'max_hours_per_day' => 10,
            ],
            [
                'id' => 15,
                'name' => 'Person Name 15',
                'type' => 'Full Time',
                'department' => 'Network',
                'rate_per_hour' => 500,
                'max_hours_per_day' => 12,
            ],
        ];

        foreach ($resources as $resource) {
            DB::table('resources')->updateOrInsert(['id' => $resource['id']], $resource);
        }
    }
}
