<?php

namespace App\Repositories;

use App\Models\ProjectMilestone;
use App\Repositories\Contracts\ProjectMilestoneRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectMilestoneRepository extends BaseRepository implements ProjectMilestoneRepositoryInterface
{
    public function __construct(ProjectMilestone $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId, ?string $status = null): Collection
    {
        $query = $this->model
            ->where('project_id', $projectId)
            ->orderBy('tanggal_target');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function getDelayed(int $projectId): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->where(function ($q) {
                $q->where('status', 'Delayed')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'Pending')
                         ->where('tanggal_target', '<', now()->toDateString());
                  });
            })
            ->orderBy('tanggal_target')
            ->get();
    }
}