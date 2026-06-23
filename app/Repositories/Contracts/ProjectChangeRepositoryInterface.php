<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ProjectChangeRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProject(int $projectId, ?string $status = null): Collection;
    public function approve(int $id, int $approvedBy): Model;
    public function reject(int $id, int $approvedBy): Model;
    public function generateNomorCr(int $projectId): string;
}