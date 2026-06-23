<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectBudgetRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectBudgetService
{
    public function __construct(
        private readonly ProjectBudgetRepositoryInterface $budgetRepository
    ) {}

    public function getByProject(int $projectId): Collection
    {
        return $this->budgetRepository->getByProject($projectId);
    }

    public function getSummary(int $projectId): array
    {
        return $this->budgetRepository->getTotalByProject($projectId);
    }

    public function create(int $projectId, array $data): Model
    {
        $data['project_id'] = $projectId;
        return $this->budgetRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->budgetRepository->update($id, $data);
    }

    public function updateRealisasi(int $id, float $realisasi): Model
    {
        return $this->budgetRepository->update($id, ['realisasi' => $realisasi]);
    }

    public function delete(int $id): bool
    {
        return $this->budgetRepository->delete($id);
    }
}