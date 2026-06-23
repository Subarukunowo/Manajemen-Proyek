<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectTaskRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId, array $filters = []): Collection;
    public function getByPhase(int $phaseId): Collection;
    public function getRootTasks(int $projectId): Collection;
    public function getSubTasks(int $parentTaskId): Collection;
    public function updateProgress(int $id, int $persen): mixed;
}