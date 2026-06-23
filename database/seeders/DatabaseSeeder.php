<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            ProjectMemberSeeder::class,
            ProjectPhaseSeeder::class,
            ProjectTaskSeeder::class,
            ProjectBudgetSeeder::class,
            ProjectRiskSeeder::class,
            ProjectMilestoneSeeder::class,
            ProjectOutsourcingSeeder::class,
            ProjectKurvaSSeeder::class,
        ]);
    }
}