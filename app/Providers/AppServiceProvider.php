<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;
use App\Models\ProjectMember;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Gate: cek apakah user adalah Project Manager di proyek tertentu
        Gate::define('manage-project', function ($user, Project $project) {
            return ProjectMember::where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->whereIn('peran', ['Owner', 'Project_Manager'])
                ->exists();
        });

        // Gate: cek apakah user adalah Owner di proyek tertentu
        Gate::define('own-project', function ($user, Project $project) {
            return $project->created_by === $user->id
                || ProjectMember::where('project_id', $project->id)
                    ->where('user_id', $user->id)
                    ->where('peran', 'Owner')
                    ->exists();
        });
    }
}
