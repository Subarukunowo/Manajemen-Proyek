<?php

namespace App\Repositories;

use App\Models\ProjectMember;
use App\Repositories\Contracts\ProjectMemberRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectMemberRepository extends BaseRepository implements ProjectMemberRepositoryInterface
{
    public function __construct(ProjectMember $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId): Collection
    {
        return $this->model
            ->where('project_id', $projectId)
            ->with('user')
            ->get();
    }

    public function findByUserAndProject(int $userId, int $projectId): mixed
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->first();
    }
}