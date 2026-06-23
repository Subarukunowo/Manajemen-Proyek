<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectPhaseRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId): Collection;
    public function reorder(int $projectId, array $orderedIds): void;
}