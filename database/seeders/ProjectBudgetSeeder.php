<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectBudget;
use Illuminate\Database\Seeder;

class ProjectBudgetSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('kode', 'PRJ-2026-001')->first();

        $budgets = [
            ['kategori' => 'Sumber Daya Manusia', 'anggaran' => 600000000,  'realisasi' => 420000000,  'keterangan' => 'Biaya tenaga kerja internal dan konsultan'],
            ['kategori' => 'Hardware & Infrastruktur', 'anggaran' => 400000000, 'realisasi' => 380000000, 'keterangan' => 'Server, jaringan, dan perangkat lapangan'],
            ['kategori' => 'Lisensi Software',    'anggaran' => 150000000,  'realisasi' => 150000000,  'keterangan' => 'Lisensi SCADA dan tools pendukung'],
            ['kategori' => 'Operasional',         'anggaran' => 200000000,  'realisasi' => 95000000,   'keterangan' => 'Transportasi, akomodasi, dan ATK'],
            ['kategori' => 'Kontinjensi',         'anggaran' => 150000000,  'realisasi' => 0,          'keterangan' => 'Cadangan anggaran risiko'],
        ];

        foreach ($budgets as $budget) {
            ProjectBudget::firstOrCreate(
                ['project_id' => $project->id, 'kategori' => $budget['kategori']],
                array_merge($budget, ['project_id' => $project->id])
            );
        }
    }
}