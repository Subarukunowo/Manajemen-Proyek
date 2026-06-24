<?php

namespace App\Http\Controllers;

use App\Http\Requests\Change\StoreChangeRequest;
use App\Http\Requests\Change\UpdateChangeRequest;
use App\Services\ProjectChangeService;
use Illuminate\Http\JsonResponse;

class ProjectChangeController extends Controller
{
    public function __construct(
        private readonly ProjectChangeService $changeService
    ) {}

    public function index(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->changeService->getByProject($projectId)]);
    }

    public function store(StoreChangeRequest $request, int $projectId): JsonResponse
    {
        $change = $this->changeService->create($projectId, $request->validated(), $request->user()->id);
        return response()->json(['data' => $change], 201);
    }

    public function update(UpdateChangeRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->changeService->update($id, $request->validated())]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->changeService->delete($id);
        return response()->json(['message' => 'Change request berhasil dihapus.']);
    }

    public function approve(int $projectId, int $id): JsonResponse
    {
        $change = $this->changeService->approve($id, request()->user()->id);
        return response()->json(['data' => $change]);
    }

    public function reject(int $projectId, int $id): JsonResponse
    {
        $change = $this->changeService->reject($id, request()->user()->id);
        return response()->json(['data' => $change]);
    }
}