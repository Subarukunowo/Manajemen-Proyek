<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectResourceRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectResourceService
{
    public function __construct(
        private readonly ProjectResourceRepositoryInterface $resourceRepository
    ) {}

    public function getByProject(int $projectId): Collection
    {
        return $this->resourceRepository->getByProject($projectId);
    }

    public function getByTask(int $taskId): Collection
    {
        return $this->resourceRepository->getByTask($taskId);
    }

    public function getTotalBiaya(int $projectId): float
    {
        return $this->resourceRepository->getTotalBiayaByProject($projectId);
    }

    public function create(int $projectId, array $data): Model
    {
        $data['project_id'] = $projectId;
        // total_biaya dihitung otomatis oleh booted() observer di model
        return $this->resourceRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->resourceRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->resourceRepository->delete($id);
    }
}