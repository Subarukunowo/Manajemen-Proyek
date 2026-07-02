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
        // ── System-level admin gate ──────────────────────────────
        Gate::define('admin', fn($user) => $user->isAdmin());

        // ── Project-level gates ──────────────────────────────────
        Gate::define('manage-project', function ($user, Project $project) {
            // System admin dapat akses semua
            if ($user->isAdmin()) return true;
            return ProjectMember::where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->whereIn('peran', ['Owner', 'Project_Manager'])
                ->exists();
        });

        Gate::define('own-project', function ($user, Project $project) {
            if ($user->isAdmin()) return true;
            return $project->created_by === $user->id
                || ProjectMember::where('project_id', $project->id)
                    ->where('user_id', $user->id)
                    ->where('peran', 'Owner')
                    ->exists();
        });
    }
}
