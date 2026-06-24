<?php

namespace App\Http\Controllers;

use App\Http\Requests\Risk\StoreRiskRequest;
use App\Http\Requests\Risk\UpdateRiskRequest;
use App\Services\ProjectRiskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectRiskController extends Controller
{
    public function __construct(
        private readonly ProjectRiskService $riskService
    ) {}

    public function index(int $projectId, Request $request): JsonResponse
    {
        return response()->json(['data' => $this->riskService->getByProject($projectId, $request->query('status'))]);
    }

    public function store(StoreRiskRequest $request, int $projectId): JsonResponse
    {
        $risk = $this->riskService->create($projectId, $request->validated());
        return response()->json(['data' => $risk], 201);
    }

    public function update(UpdateRiskRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->riskService->update($id, $request->validated())]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->riskService->delete($id);
        return response()->json(['message' => 'Risiko berhasil dihapus.']);
    }

    public function critical(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->riskService->getCritical($projectId)]);
    }

    public function updateStatus(Request $request, int $projectId, int $id): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:Identified,Mitigated,Occurred,Closed']]);
        return response()->json(['data' => $this->riskService->updateStatus($id, $request->input('status'))]);
    }
}