<?php

namespace App\Repositories;

use App\Models\ProjectRisk;
use App\Repositories\Contracts\ProjectRiskRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectRiskRepository extends BaseRepository implements ProjectRiskRepositoryInterface
{
    public function __construct(ProjectRisk $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId, ?string $status = null): Collection
    {
        $query = $this->model
            ->where('project_id', $projectId)
            ->with('assignee')
            ->orderBy('skor_risiko', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function getCritical(int $projectId): Collection
    {
        // Skor 9 = High x High
        return $this->model
            ->where('project_id', $projectId)
            ->where('skor_risiko', '>=', 6)
            ->whereNotIn('status', ['Mitigated', 'Closed'])
            ->with('assignee')
            ->orderByDesc('skor_risiko')
            ->get();
    }
}