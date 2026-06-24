<?php

namespace App\Http\Controllers;

use App\Http\Requests\Phase\StorePhaseRequest;
use App\Http\Requests\Phase\UpdatePhaseRequest;
use App\Services\ProjectPhaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectPhaseController extends Controller
{
    public function __construct(
        private readonly ProjectPhaseService $phaseService
    ) {}

    public function index(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->phaseService->getByProject($projectId)]);
    }

    public function store(StorePhaseRequest $request, int $projectId): JsonResponse
    {
        $phase = $this->phaseService->create($projectId, $request->validated());
        return response()->json(['data' => $phase], 201);
    }

    public function update(UpdatePhaseRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->phaseService->update($id, $request->validated())]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->phaseService->delete($id);
        return response()->json(['message' => 'Phase berhasil dihapus.']);
    }

    public function reorder(Request $request, int $projectId): JsonResponse
    {
        $request->validate([
            'ordered_ids'   => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:project_phases,id'],
        ]);
        $this->phaseService->reorder($projectId, $request->input('ordered_ids'));
        return response()->json(['message' => 'Urutan phase berhasil diperbarui.']);
    }
}

