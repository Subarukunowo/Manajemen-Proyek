<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectTaskSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('kode', 'PRJ-2026-001')->first();
        $phase   = ProjectPhase::where('project_id', $project->id)->where('urutan', 3)->first();
        $budi    = User::where('email', 'budi@dashboard.com')->first();
        $siti    = User::where('email', 'siti@dashboard.com')->first();
        $ahmad   = User::where('email', 'ahmad@dashboard.com')->first();

        $tasks = [
            [
                'nama'            => 'Setup Environment & CI/CD',
                'deskripsi'       => 'Konfigurasi environment development dan pipeline CI/CD.',
                'tanggal_mulai'   => '2026-05-01',
                'tanggal_selesai' => '2026-05-15',
                'durasi_hari'     => 15,
                'persen_selesai'  => 100,
                'status'          => 'Done',
                'prioritas'       => 'High',
                'assigned_to'     => $budi->id,
            ],
            [
                'nama'            => 'Pengembangan Modul Backend API',
                'deskripsi'       => 'Pembuatan REST API untuk integrasi sistem monitoring.',
                'tanggal_mulai'   => '2026-05-16',
                'tanggal_selesai' => '2026-07-31',
                'durasi_hari'     => 76,
                'persen_selesai'  => 65,
                'status'          => 'In_Progress',
                'prioritas'       => 'Critical',
                'assigned_to'     => $ahmad->id,
            ],
            [
                'nama'            => 'Pengembangan Dashboard Frontend',
                'deskripsi'       => 'Pembuatan UI dashboard menggunakan Bulma CSS.',
                'tanggal_mulai'   => '2026-06-01',
                'tanggal_selesai' => '2026-08-31',
                'durasi_hari'     => 91,
                'persen_selesai'  => 40,
                'status'          => 'In_Progress',
                'prioritas'       => 'High',
                'assigned_to'     => $siti->id,
            ],
        ];

        foreach ($tasks as $task) {
            ProjectTask::firstOrCreate(
                ['project_id' => $project->id, 'nama' => $task['nama']],
                array_merge($task, ['project_id' => $project->id, 'phase_id' => $phase->id])
            );
        }
    }
}