<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectMemberRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId): Collection;
    public function findByUserAndProject(int $userId, int $projectId): mixed;
}