<?php

namespace App\Repositories;

use App\Models\ProjectTask;
use App\Repositories\Contracts\ProjectTaskRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectTaskRepository extends BaseRepository implements ProjectTaskRepositoryInterface
{
    public function __construct(ProjectTask $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId, array $filters = []): Collection
    {
        $query = $this->model
            ->where('project_id', $projectId)
            ->with(['phase', 'assignee', 'subTasks']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['prioritas'])) {
            $query->where('prioritas', $filters['prioritas']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->get();
    }

    public function getByPhase(int $phaseId): Collection
    {
        return $this->model
            ->where('phase_id', $phaseId)
            ->whereNull('parent_task_id')
            ->with(['subTasks', 'assignee'])
            ->get();
    }

    public function getRootTasks(int $projectId): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->whereNull('parent_task_id')
            ->with(['subTasks.subTasks', 'assignee'])
            ->get();
    }

    public function getSubTasks(int $parentTaskId): Collection
    {
        return $this->model
            ->where('parent_task_id', $parentTaskId)
            ->with(['assignee'])
            ->get();
    }

    public function updateProgress(int $id, int $persen): mixed
    {
        $task = $this->model->findOrFail($id);
        $task->update([
            'persen_selesai' => min(100, max(0, $persen)),
            'status' => $persen >= 100 ? 'Done' : ($persen > 0 ? 'In_Progress' : $task->status),
        ]);
        return $task->fresh();
    }
}