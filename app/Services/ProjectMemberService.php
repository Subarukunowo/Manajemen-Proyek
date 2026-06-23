<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectMemberRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectMemberService
{
    public function __construct(
        private readonly ProjectMemberRepositoryInterface $memberRepository
    ) {}

    public function getByProject(int $projectId): Collection
    {
        return $this->memberRepository->getByProject($projectId);
    }

    public function add(int $projectId, array $data): Model
    {
        $data['project_id']      = $projectId;
        $data['tanggal_bergabung'] = $data['tanggal_bergabung'] ?? now()->toDateString();
        return $this->memberRepository->create($data);
    }

    public function updatePeran(int $id, string $peran): Model
    {
        return $this->memberRepository->update($id, ['peran' => $peran]);
    }

    public function remove(int $id): bool
    {
        return $this->memberRepository->delete($id);
    }
}