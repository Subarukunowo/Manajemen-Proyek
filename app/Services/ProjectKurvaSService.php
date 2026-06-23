<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectKurvaSRepositoryInterface;
use App\Repositories\Contracts\ProjectTaskRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectKurvaSService
{
    public function __construct(
        private readonly ProjectKurvaSRepositoryInterface $kurvaSRepository,
        private readonly ProjectTaskRepositoryInterface   $taskRepository
    ) {}

    public function getByProject(int $projectId): Collection
    {
        return $this->kurvaSRepository->getByProject($projectId);
    }

    public function getLatest(int $projectId): mixed
    {
        return $this->kurvaSRepository->getLatest($projectId);
    }

    /**
     * Format data siap pakai untuk Chart.js:
     * { labels: [], rencana: [], realisasi: [] }
     */
    public function getChartData(int $projectId): array
    {
        $records = $this->kurvaSRepository->getByProject($projectId);

        return [
            'labels'     => $records->pluck('periode')->map(fn ($d) => $d->format('d M Y'))->toArray(),
            'rencana'    => $records->pluck('rencana_kumulatif')->map(fn ($v) => (float) $v)->toArray(),
            'realisasi'  => $records->pluck('realisasi_kumulatif')->map(fn ($v) => (float) $v)->toArray(),
            'deviasi'    => $records->map(fn ($r) => round((float) $r->realisasi_kumulatif - (float) $r->rencana_kumulatif, 2))->toArray(),
        ];
    }

    /**
     * Snapshot manual: input rencana & realisasi untuk periode tertentu.
     */
    public function upsert(int $projectId, string $periode, array $data): Model
    {
        return $this->kurvaSRepository->upsertPeriode($projectId, $periode, $data);
    }

    /**
     * Snapshot otomatis: hitung realisasi_kumulatif dari rata-rata persen_selesai task.
     */
    public function takeAutoSnapshot(int $projectId, string $periode, float $rencanaCumulative): Model
    {
        $tasks             = $this->taskRepository->getByProject($projectId);
        $realisasiCumul    = $tasks->isEmpty() ? 0 : round($tasks->avg('persen_selesai'), 2);
        $latest            = $this->kurvaSRepository->getLatest($projectId);
        $prevRealisasi     = $latest ? (float) $latest->realisasi_kumulatif : 0;
        $prevRencana       = $latest ? (float) $latest->rencana_kumulatif   : 0;

        return $this->kurvaSRepository->upsertPeriode($projectId, $periode, [
            'rencana_kumulatif'   => $rencanaCumulative,
            'realisasi_kumulatif' => $realisasiCumul,
            'rencana_periode'     => round($rencanaCumulative - $prevRencana, 2),
            'realisasi_periode'   => round($realisasiCumul - $prevRealisasi, 2),
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->kurvaSRepository->delete($id);
    }
}