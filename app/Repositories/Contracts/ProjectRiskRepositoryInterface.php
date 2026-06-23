<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectRiskRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId, ?string $status = null): Collection;
    public function getCritical(int $projectId): Collection;
}