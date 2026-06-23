<?php

namespace App\Repositories;

use App\Models\ProjectBudget;
use App\Repositories\Contracts\ProjectBudgetRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectBudgetRepository extends BaseRepository implements ProjectBudgetRepositoryInterface
{
    public function __construct(ProjectBudget $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->orderBy('kategori')
            ->get();
    }

    public function getTotalByProject(int $projectId): array
    {
        $result = $this->model
            ->where('project_id', $projectId)
            ->selectRaw('SUM(anggaran) as total_anggaran, SUM(realisasi) as total_realisasi')
            ->first();

        return [
            'total_anggaran'  => (float) ($result->total_anggaran ?? 0),
            'total_realisasi' => (float) ($result->total_realisasi ?? 0),
            'variansi'        => (float) ($result->total_anggaran ?? 0) - (float) ($result->total_realisasi ?? 0),
        ];
    }
}