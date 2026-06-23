<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectRisk;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectRiskSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('kode', 'PRJ-2026-001')->first();
        $budi    = User::where('email', 'budi@dashboard.com')->first();
        $ahmad   = User::where('email', 'ahmad@dashboard.com')->first();

        $risks = [
            [
                'deskripsi_risiko' => 'Keterlambatan pengiriman perangkat hardware dari vendor',
                'probabilitas'     => 'High',
                'dampak'           => 'High',
                'mitigasi'         => 'Lakukan pemesanan lebih awal dan siapkan vendor alternatif',
                'status'           => 'Identified',
                'assigned_to'      => $budi->id,
            ],
            [
                'deskripsi_risiko' => 'Perubahan regulasi teknis dari PLN pusat',
                'probabilitas'     => 'Medium',
                'dampak'           => 'High',
                'mitigasi'         => 'Pantau regulasi secara berkala dan siapkan mekanisme CR',
                'status'           => 'Identified',
                'assigned_to'      => $budi->id,
            ],
            [
                'deskripsi_risiko' => 'Kerentanan keamanan pada API integrasi',
                'probabilitas'     => 'Medium',
                'dampak'           => 'High',
                'mitigasi'         => 'Lakukan penetration testing sebelum go-live',
                'status'           => 'Mitigated',
                'assigned_to'      => $ahmad->id,
            ],
            [
                'deskripsi_risiko' => 'Rotasi personel kunci di tengah proyek',
                'probabilitas'     => 'Low',
                'dampak'           => 'Medium',
                'mitigasi'         => 'Dokumentasi knowledge transfer dan backup SDM',
                'status'           => 'Identified',
                'assigned_to'      => $budi->id,
            ],
        ];

        foreach ($risks as $risk) {
            ProjectRisk::firstOrCreate(
                ['project_id' => $project->id, 'deskripsi_risiko' => $risk['deskripsi_risiko']],
                array_merge($risk, ['project_id' => $project->id])
            );
        }
    }
}