<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectOutsourcingRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectOutsourcingService
{
    public function __construct(
        private readonly ProjectOutsourcingRepositoryInterface $outsourcingRepository
    ) {}

    public function getByProject(int $projectId, ?string $status = null): Collection
    {
        return $this->outsourcingRepository->getByProject($projectId, $status);
    }

    public function getTotalKontrak(int $projectId): float
    {
        return $this->outsourcingRepository->getTotalKontrak($projectId);
    }

    public function create(int $projectId, array $data): Model
    {
        $data['project_id'] = $projectId;
        return $this->outsourcingRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->outsourcingRepository->update($id, $data);
    }

    public function updateProgress(int $id, int $persen): Model
    {
        return $this->outsourcingRepository->update($id, [
            'persen_selesai' => min(100, max(0, $persen)),
        ]);
    }

    public function terminate(int $id): Model
    {
        return $this->outsourcingRepository->update($id, ['status' => 'Terminated']);
    }

    public function delete(int $id): bool
    {
        return $this->outsourcingRepository->delete($id);
    }
}