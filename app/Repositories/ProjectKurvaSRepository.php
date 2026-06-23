<?php

namespace App\Repositories;

use App\Models\ProjectKurvaS;
use App\Repositories\Contracts\ProjectKurvaSRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectKurvaSRepository extends BaseRepository implements ProjectKurvaSRepositoryInterface
{
    public function __construct(ProjectKurvaS $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->orderBy('periode')
            ->get();
    }

    public function getLatest(int $projectId): mixed
    {
        return $this->model
            ->where('project_id', $projectId)
            ->orderByDesc('periode')
            ->first();
    }

    public function upsertPeriode(int $projectId, string $periode, array $data): mixed
    {
        return $this->model->updateOrCreate(
            ['project_id' => $projectId, 'periode' => $periode],
            $data
        );
    }
}