<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectKurvaSRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId): Collection;
    public function getLatest(int $projectId): mixed;
    public function upsertPeriode(int $projectId, string $periode, array $data): mixed;
}