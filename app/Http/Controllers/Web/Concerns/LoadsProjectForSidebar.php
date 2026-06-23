<?php

namespace App\Http\Controllers\Web\Concerns;

use App\Models\Project;

trait LoadsProjectForSidebar
{
    /**
     * Load the minimal relations needed by the sidebar badges
     * (phases count, tasks count, risks open count, changes pending count).
     */
    protected function loadProjectForSidebar(Project $project): Project
    {
        if (! $project->relationLoaded('phases')) {
            $project->loadCount('phases');
            $project->load(['phases']);
        }
        if (! $project->relationLoaded('tasks')) {
            $project->load(['tasks:id,project_id,status']);
        }
        if (! $project->relationLoaded('risks')) {
            $project->load(['risks:id,project_id,status']);
        }
        if (! $project->relationLoaded('changes')) {
            $project->load(['changes:id,project_id,status']);
        }
        return $project;
    }
}
