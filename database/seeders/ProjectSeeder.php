<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@dashboard.com')->first();

        $projects = [
            [
                'kode'            => 'PRJ-2026-001',
                'nama'            => 'Sistem Monitoring Jaringan Distribusi',
                'deskripsi'       => 'Pembangunan sistem monitoring real-time untuk jaringan distribusi listrik.',
                'status'          => 'Active',
                'tanggal_mulai'   => '2026-01-01',
                'tanggal_selesai' => '2026-12-31',
                'anggaran'        => 1500000000,
                'created_by'      => $admin->id,
            ],
            [
                'kode'            => 'PRJ-2026-002',
                'nama'            => 'Digitalisasi Gardu Induk',
                'deskripsi'       => 'Digitalisasi sistem kontrol gardu induk berbasis SCADA.',
                'status'          => 'Draft',
                'tanggal_mulai'   => '2026-03-01',
                'tanggal_selesai' => '2026-11-30',
                'anggaran'        => 2800000000,
                'created_by'      => $admin->id,
            ],
        ];

        foreach ($projects as $project) {
            Project::firstOrCreate(['kode' => $project['kode']], $project);
        }
    }
}