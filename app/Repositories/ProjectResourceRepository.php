<?php

namespace App\Repositories;

use App\Models\ProjectResource;
use App\Repositories\Contracts\ProjectResourceRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectResourceRepository extends BaseRepository implements ProjectResourceRepositoryInterface
{
    public function __construct(ProjectResource $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->with('task')
            ->get();
    }

    public function getByTask(int $taskId): Collection
    {
        return $this->model
            ->where('task_id', $taskId)
            ->get();
    }

    public function getTotalBiayaByProject(int $projectId): float
    {
        return (float) $this->model
            ->where('project_id', $projectId)
            ->sum('total_biaya');
    }
}