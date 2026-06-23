<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function allWithSummary(): Collection
    {
        return $this->model
            ->with(['creator', 'members', 'milestones'])
            ->withCount(['tasks', 'risks', 'changes'])
            ->get();
    }

    public function findByKode(string $kode): mixed
    {
        return $this->model->where('kode', $kode)->first();
    }

    public function findWithFullRelations(int $id): mixed
    {
        return $this->model->with([
            'creator',
            'phases.tasks',
            'members.user',
            'budgetBreakdowns',
            'risks',
            'changes',
            'milestones',
            'outsourcings',
            'sCurveRecords',
        ])->findOrFail($id);
    }

    public function filterByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }
}