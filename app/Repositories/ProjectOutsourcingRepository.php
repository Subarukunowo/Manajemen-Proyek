<?php

namespace App\Repositories;

use App\Models\ProjectOutsourcing;
use App\Repositories\Contracts\ProjectOutsourcingRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectOutsourcingRepository extends BaseRepository implements ProjectOutsourcingRepositoryInterface
{
    public function __construct(ProjectOutsourcing $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId, ?string $status = null): Collection
    {
        $query = $this->model
            ->where('project_id', $projectId)
            ->orderByDesc('tanggal_mulai');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function getTotalKontrak(int $projectId): float
    {
        return (float) $this->model
            ->where('project_id', $projectId)
            ->sum('nilai_kontrak');
    }
}