<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectMemberSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('kode', 'PRJ-2026-001')->first();

        $members = [
            ['email' => 'admin@dashboard.com', 'peran' => 'Owner'],
            ['email' => 'budi@dashboard.com',  'peran' => 'Project_Manager'],
            ['email' => 'siti@dashboard.com',  'peran' => 'Developer'],
            ['email' => 'ahmad@dashboard.com', 'peran' => 'Developer'],
            ['email' => 'dewi@dashboard.com',  'peran' => 'Auditor'],
        ];

        foreach ($members as $member) {
            $user = User::where('email', $member['email'])->first();
            ProjectMember::firstOrCreate(
                ['project_id' => $project->id, 'user_id' => $user->id],
                ['peran' => $member['peran'], 'tanggal_bergabung' => '2026-01-01']
            );
        }
    }
}