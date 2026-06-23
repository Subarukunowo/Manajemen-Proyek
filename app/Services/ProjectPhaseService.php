<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectPhaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectPhaseService
{
    public function __construct(
        private readonly ProjectPhaseRepositoryInterface $phaseRepository
    ) {}

    public function getByProject(int $projectId): Collection
    {
        return $this->phaseRepository->getByProject($projectId);
    }

    public function create(int $projectId, array $data): Model
    {
        $existing = $this->phaseRepository->getByProject($projectId);
        $data['project_id'] = $projectId;
        $data['urutan']     = $existing->count() + 1;

        return $this->phaseRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->phaseRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->phaseRepository->delete($id);
    }

    public function reorder(int $projectId, array $orderedIds): void
    {
        $this->phaseRepository->reorder($projectId, $orderedIds);
    }
}