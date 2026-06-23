<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectTaskRepositoryInterface;
use App\Repositories\Contracts\ProjectPhaseRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectTaskService
{
    public function __construct(
        private readonly ProjectTaskRepositoryInterface $taskRepository,
        private readonly ProjectPhaseRepositoryInterface $phaseRepository,
        private readonly ProjectRepositoryInterface $projectRepository
    ) {}

    public function getByProject(int $projectId, array $filters = []): Collection
    {
        return $this->taskRepository->getByProject($projectId, $filters);
    }

    public function getByPhase(int $phaseId): Collection
    {
        return $this->taskRepository->getByPhase($phaseId);
    }

    public function getRootTasks(int $projectId): Collection
    {
        return $this->taskRepository->getRootTasks($projectId);
    }

    public function create(int $projectId, array $data): Model
    {
        $data['project_id'] = $projectId;
        return $this->taskRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $task = $this->taskRepository->update($id, $data);
        $this->rollupProgress($task->project_id);
        return $task;
    }

    public function delete(int $id): bool
    {
        $task = $this->taskRepository->findById($id);
        $projectId = $task?->project_id;
        $result = $this->taskRepository->delete($id);

        if ($result && $projectId) {
            $this->rollupProgress($projectId);
        }

        return $result;
    }

    public function updateProgress(int $id, int $persen): Model
    {
        $task = $this->taskRepository->updateProgress($id, $persen);
        $this->rollupProgress($task->project_id);
        return $task;
    }

    /**
     * Hitung rata-rata persen_selesai semua task di project
     * lalu update kolom di tabel projects (jika ada kolom persen_selesai).
     * Saat ini menyimpan ke cache/log; implementasi update project
     * disesuaikan jika kolom ditambahkan ke tabel projects.
     */
    private function rollupProgress(int $projectId): void
    {
        $tasks = $this->taskRepository->getByProject($projectId);

        if ($tasks->isEmpty()) {
            return;
        }

        $avgProgress = $tasks->avg('persen_selesai');

        // Rollup ke tiap phase
        $phases = $this->phaseRepository->getByProject($projectId);
        foreach ($phases as $phase) {
            $phaseTasks = $tasks->where('phase_id', $phase->id);
            if ($phaseTasks->isNotEmpty()) {
                $phaseAvg = $phaseTasks->avg('persen_selesai');
                $status   = match(true) {
                    $phaseAvg >= 100 => 'Completed',
                    $phaseAvg > 0    => 'On_Progress',
                    default          => 'Pending',
                };
                $this->phaseRepository->update($phase->id, ['status' => $status]);
            }
        }
    }
}