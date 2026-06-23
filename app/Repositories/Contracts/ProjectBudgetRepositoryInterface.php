<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectBudgetRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId): Collection;
    public function getTotalByProject(int $projectId): array;
}