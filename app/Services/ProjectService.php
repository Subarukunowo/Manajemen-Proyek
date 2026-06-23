<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository
    ) {}

    public function getAllWithSummary(): Collection
    {
        return $this->projectRepository->allWithSummary();
    }

    public function getDetail(int $id): Model
    {
        return $this->projectRepository->findWithFullRelations($id);
    }

    public function create(array $data, int $createdBy): Model
    {
        $data['created_by'] = $createdBy;
        $data['kode']       = $this->generateKode();

        return $this->projectRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->projectRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->projectRepository->delete($id);
    }

    public function updateStatus(int $id, string $status): Model
    {
        return $this->projectRepository->update($id, ['status' => $status]);
    }

    private function generateKode(): string
    {
        $year    = now()->year;
        $count   = $this->projectRepository->filterByStatus('Active')->count() + 1;
        return sprintf('PRJ-%d-%03d', $year, $count);
    }
}