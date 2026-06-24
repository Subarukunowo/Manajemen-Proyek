<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resource\StoreResourceRequest;
use App\Http\Requests\Resource\UpdateResourceRequest;
use App\Services\ProjectResourceService;
use Illuminate\Http\JsonResponse;

class ProjectResourceController extends Controller
{
    public function __construct(
        private readonly ProjectResourceService $resourceService
    ) {}

    public function index(int $projectId): JsonResponse
    {
        return response()->json([
            'data'        => $this->resourceService->getByProject($projectId),
            'total_biaya' => $this->resourceService->getTotalBiaya($projectId),
        ]);
    }

    public function store(StoreResourceRequest $request, int $projectId): JsonResponse
    {
        $resource = $this->resourceService->create($projectId, $request->validated());
        return response()->json(['data' => $resource], 201);
    }

    public function update(UpdateResourceRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->resourceService->update($id, $request->validated())]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->resourceService->delete($id);
        return response()->json(['message' => 'Resource berhasil dihapus.']);
    }
}