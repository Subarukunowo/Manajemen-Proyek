<?php

namespace App\Repositories;

use App\Models\ProjectPhase;
use App\Repositories\Contracts\ProjectPhaseRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectPhaseRepository extends BaseRepository implements ProjectPhaseRepositoryInterface
{
    public function __construct(ProjectPhase $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->with('tasks')
            ->orderBy('urutan')
            ->get();
    }

    public function reorder(int $projectId, array $orderedIds): void
    {
        foreach ($orderedIds as $urutan => $id) {
            $this->model
                ->where('id', $id)
                ->where('project_id', $projectId)
                ->update(['urutan' => $urutan + 1]);
        }
    }
}