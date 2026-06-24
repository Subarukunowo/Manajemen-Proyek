<?php

namespace App\Http\Controllers;

use App\Http\Requests\KurvaS\StoreKurvaSRequest;
use App\Services\ProjectKurvaSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectKurvaSController extends Controller
{
    public function __construct(
        private readonly ProjectKurvaSService $kurvaSService
    ) {}

    public function index(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->kurvaSService->getByProject($projectId)]);
    }

    public function store(StoreKurvaSRequest $request, int $projectId): JsonResponse
    {
        $record = $this->kurvaSService->upsert($projectId, $request->input('periode'), $request->validated());
        return response()->json(['data' => $record], 201);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->kurvaSService->delete($id);
        return response()->json(['message' => 'Data kurva-S berhasil dihapus.']);
    }

    public function chartData(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->kurvaSService->getChartData($projectId)]);
    }

    public function latest(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->kurvaSService->getLatest($projectId)]);
    }

    public function snapshot(Request $request, int $projectId): JsonResponse
    {
        $request->validate([
            'periode'           => ['required', 'date'],
            'rencana_kumulatif' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
        $record = $this->kurvaSService->takeAutoSnapshot(
            $projectId,
            $request->input('periode'),
            $request->input('rencana_kumulatif')
        );
        return response()->json(['data' => $record]);
    }
}