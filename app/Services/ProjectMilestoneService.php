<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectMilestoneRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectMilestoneService
{
    public function __construct(
        private readonly ProjectMilestoneRepositoryInterface $milestoneRepository
    ) {}

    public function getByProject(int $projectId, ?string $status = null): Collection
    {
        return $this->milestoneRepository->getByProject($projectId, $status);
    }

    public function getDelayed(int $projectId): Collection
    {
        return $this->milestoneRepository->getDelayed($projectId);
    }

    public function create(int $projectId, array $data): Model
    {
        $data['project_id'] = $projectId;
        return $this->milestoneRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->milestoneRepository->update($id, $data);
    }

    public function achieve(int $id): Model
    {
        return $this->milestoneRepository->update($id, [
            'status'          => 'Achieved',
            'tanggal_aktual'  => now()->toDateString(),
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->milestoneRepository->delete($id);
    }
}