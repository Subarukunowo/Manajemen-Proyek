<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectResourceRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId): Collection;
    public function getByTask(int $taskId): Collection;
    public function getTotalBiayaByProject(int $projectId): float;
}