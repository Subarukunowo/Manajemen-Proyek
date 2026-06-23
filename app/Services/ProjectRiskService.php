<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectRiskRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectRiskService
{
    public function __construct(
        private readonly ProjectRiskRepositoryInterface $riskRepository
    ) {}

    public function getByProject(int $projectId, ?string $status = null): Collection
    {
        return $this->riskRepository->getByProject($projectId, $status);
    }

    public function getCritical(int $projectId): Collection
    {
        return $this->riskRepository->getCritical($projectId);
    }

    public function create(int $projectId, array $data): Model
    {
        $data['project_id'] = $projectId;
        // skor_risiko dihitung otomatis oleh booted() observer di model
        return $this->riskRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->riskRepository->update($id, $data);
    }

    public function updateStatus(int $id, string $status): Model
    {
        return $this->riskRepository->update($id, ['status' => $status]);
    }

    public function delete(int $id): bool
    {
        return $this->riskRepository->delete($id);
    }
}