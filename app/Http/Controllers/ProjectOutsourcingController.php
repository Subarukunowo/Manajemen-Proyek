<?php

namespace App\Http\Controllers;

use App\Http\Requests\Outsourcing\StoreOutsourcingRequest;
use App\Http\Requests\Outsourcing\UpdateOutsourcingRequest;
use App\Services\ProjectOutsourcingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectOutsourcingController extends Controller
{
    public function __construct(
        private readonly ProjectOutsourcingService $outsourcingService
    ) {}

    public function index(int $projectId, Request $request): JsonResponse
    {
        return response()->json([
            'data'          => $this->outsourcingService->getByProject($projectId, $request->query('status')),
            'total_kontrak' => $this->outsourcingService->getTotalKontrak($projectId),
        ]);
    }

    public function store(StoreOutsourcingRequest $request, int $projectId): JsonResponse
    {
        $outsourcing = $this->outsourcingService->create($projectId, $request->validated());
        return response()->json(['data' => $outsourcing], 201);
    }

    public function update(UpdateOutsourcingRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->outsourcingService->update($id, $request->validated())]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->outsourcingService->delete($id);
        return response()->json(['message' => 'Data outsourcing berhasil dihapus.']);
    }

    public function updateProgress(Request $request, int $projectId, int $id): JsonResponse
    {
        $request->validate(['persen_selesai' => ['required', 'integer', 'min:0', 'max:100']]);
        return response()->json(['data' => $this->outsourcingService->updateProgress($id, $request->input('persen_selesai'))]);
    }

    public function terminate(int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->outsourcingService->terminate($id)]);
    }
}