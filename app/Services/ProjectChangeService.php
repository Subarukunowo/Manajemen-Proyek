<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectChangeRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectChangeService
{
    public function __construct(
        private readonly ProjectChangeRepositoryInterface $changeRepository
    ) {}

    public function getByProject(int $projectId, ?string $status = null): Collection
    {
        return $this->changeRepository->getByProject($projectId, $status);
    }

    public function create(int $projectId, array $data, int $requestedBy): Model
    {
        $data['project_id']    = $projectId;
        $data['requested_by']  = $requestedBy;
        $data['nomor_cr']      = $this->changeRepository->generateNomorCr($projectId);
        $data['tanggal_request'] = now()->toDateString();
        $data['status']        = 'Pending';

        return $this->changeRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->changeRepository->update($id, $data);
    }

    public function approve(int $id, int $approvedBy): Model
    {
        return $this->changeRepository->approve($id, $approvedBy);
    }

    public function reject(int $id, int $approvedBy): Model
    {
        return $this->changeRepository->reject($id, $approvedBy);
    }

    public function delete(int $id): bool
    {
        return $this->changeRepository->delete($id);
    }
}