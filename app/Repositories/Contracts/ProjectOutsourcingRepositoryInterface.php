<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectOutsourcingRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId, ?string $status = null): Collection;
    public function getTotalKontrak(int $projectId): float;
}