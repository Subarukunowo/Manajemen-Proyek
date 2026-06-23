<?php

namespace App\Repositories;

use App\Models\ProjectChange;
use App\Repositories\Contracts\ProjectChangeRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectChangeRepository extends BaseRepository implements ProjectChangeRepositoryInterface
{
    public function __construct(ProjectChange $model)
    {
        parent::__construct($model);
    }

    public function getByProject(int $projectId, ?string $status = null): Collection
    {
        $query = $this->model
            ->where('project_id', $projectId)
            ->with(['requester', 'approver'])
            ->orderByDesc('tanggal_request');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function approve(int $id, int $approvedBy): Model
    {
        $cr = $this->model->findOrFail($id);
        $cr->update([
            'status'          => 'Approved',
            'approved_by'     => $approvedBy,
            'tanggal_approval' => now()->toDateString(),
        ]);
        return $cr->fresh(['requester', 'approver']);
    }

    public function reject(int $id, int $approvedBy): Model
    {
        $cr = $this->model->findOrFail($id);
        $cr->update([
            'status'          => 'Rejected',
            'approved_by'     => $approvedBy,
            'tanggal_approval' => now()->toDateString(),
        ]);
        return $cr->fresh(['requester', 'approver']);
    }

    public function generateNomorCr(int $projectId): string
    {
        $count = $this->model->where('project_id', $projectId)->count() + 1;
        return sprintf('CR-%d-%03d', $projectId, $count);
    }
}