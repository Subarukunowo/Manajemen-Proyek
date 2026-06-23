<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Database\Seeder;

class ProjectPhaseSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('kode', 'PRJ-2026-001')->first();

        $phases = [
            ['nama' => 'Inisiasi & Perencanaan',  'urutan' => 1, 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-02-28', 'status' => 'Completed'],
            ['nama' => 'Desain Sistem',            'urutan' => 2, 'tanggal_mulai' => '2026-03-01', 'tanggal_selesai' => '2026-04-30', 'status' => 'Completed'],
            ['nama' => 'Pengembangan',             'urutan' => 3, 'tanggal_mulai' => '2026-05-01', 'tanggal_selesai' => '2026-08-31', 'status' => 'On_Progress'],
            ['nama' => 'Testing & QA',             'urutan' => 4, 'tanggal_mulai' => '2026-09-01', 'tanggal_selesai' => '2026-10-31', 'status' => 'Pending'],
            ['nama' => 'Deployment & Go-Live',     'urutan' => 5, 'tanggal_mulai' => '2026-11-01', 'tanggal_selesai' => '2026-12-31', 'status' => 'Pending'],
        ];

        foreach ($phases as $phase) {
            ProjectPhase::firstOrCreate(
                ['project_id' => $project->id, 'urutan' => $phase['urutan']],
                array_merge($phase, ['project_id' => $project->id])
            );
        }
    }
}