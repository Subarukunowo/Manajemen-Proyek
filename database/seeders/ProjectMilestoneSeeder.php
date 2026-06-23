<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Database\Seeder;

class ProjectMilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('kode', 'PRJ-2026-001')->first();

        $milestones = [
            ['nama' => 'Kick-off Meeting',          'tanggal_target' => '2026-01-05', 'tanggal_aktual' => '2026-01-05', 'status' => 'Achieved'],
            ['nama' => 'Selesai Desain Arsitektur', 'tanggal_target' => '2026-04-30', 'tanggal_aktual' => '2026-04-28', 'status' => 'Achieved'],
            ['nama' => 'Demo Internal v1.0',        'tanggal_target' => '2026-07-31', 'tanggal_aktual' => null,         'status' => 'Pending'],
            ['nama' => 'UAT Sign-off',              'tanggal_target' => '2026-10-31', 'tanggal_aktual' => null,         'status' => 'Pending'],
            ['nama' => 'Go-Live Produksi',          'tanggal_target' => '2026-12-01', 'tanggal_aktual' => null,         'status' => 'Pending'],
        ];

        foreach ($milestones as $milestone) {
            ProjectMilestone::firstOrCreate(
                ['project_id' => $project->id, 'nama' => $milestone['nama']],
                array_merge($milestone, ['project_id' => $project->id])
            );
        }
    }
}