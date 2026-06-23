<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    public function allWithSummary(): Collection;
    public function findByKode(string $kode): mixed;
    public function findWithFullRelations(int $id): mixed;
    public function filterByStatus(string $status): Collection;
}