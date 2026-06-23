<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectKurvaS;
use Illuminate\Database\Seeder;

class ProjectKurvaSSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('kode', 'PRJ-2026-001')->first();

        $records = [
            ['periode' => '2026-01-31', 'rencana_kumulatif' => 5.00,  'realisasi_kumulatif' => 4.50,  'rencana_periode' => 5.00,  'realisasi_periode' => 4.50],
            ['periode' => '2026-02-28', 'rencana_kumulatif' => 12.00, 'realisasi_kumulatif' => 11.20, 'rencana_periode' => 7.00,  'realisasi_periode' => 6.70],
            ['periode' => '2026-03-31', 'rencana_kumulatif' => 20.00, 'realisasi_kumulatif' => 18.50, 'rencana_periode' => 8.00,  'realisasi_periode' => 7.30],
            ['periode' => '2026-04-30', 'rencana_kumulatif' => 30.00, 'realisasi_kumulatif' => 28.00, 'rencana_periode' => 10.00, 'realisasi_periode' => 9.50],
            ['periode' => '2026-05-31', 'rencana_kumulatif' => 42.00, 'realisasi_kumulatif' => 38.50, 'rencana_periode' => 12.00, 'realisasi_periode' => 10.50],
            ['periode' => '2026-06-30', 'rencana_kumulatif' => 55.00, 'realisasi_kumulatif' => 49.00, 'rencana_periode' => 13.00, 'realisasi_periode' => 10.50],
        ];

        foreach ($records as $record) {
            ProjectKurvaS::firstOrCreate(
                ['project_id' => $project->id, 'periode' => $record['periode']],
                array_merge($record, ['project_id' => $project->id])
            );
        }
    }
}