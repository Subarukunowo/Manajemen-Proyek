<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectOutsourcing;
use Illuminate\Database\Seeder;

class ProjectOutsourcingSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('kode', 'PRJ-2026-001')->first();

        $outsourcings = [
            [
                'nama_vendor'     => 'PT Teknologi Nusantara',
                'lingkup_kerja'   => 'Pengadaan dan instalasi perangkat sensor lapangan',
                'nilai_kontrak'   => 350000000,
                'tanggal_mulai'   => '2026-03-01',
                'tanggal_selesai' => '2026-07-31',
                'status'          => 'Active',
                'persen_selesai'  => 70,
            ],
            [
                'nama_vendor'     => 'CV Sistem Integra',
                'lingkup_kerja'   => 'Integrasi SCADA dengan sistem monitoring baru',
                'nilai_kontrak'   => 250000000,
                'tanggal_mulai'   => '2026-05-01',
                'tanggal_selesai' => '2026-09-30',
                'status'          => 'Active',
                'persen_selesai'  => 45,
            ],
        ];

        foreach ($outsourcings as $outsourcing) {
            ProjectOutsourcing::firstOrCreate(
                ['project_id' => $project->id, 'nama_vendor' => $outsourcing['nama_vendor']],
                array_merge($outsourcing, ['project_id' => $project->id])
            );
        }
    }
}