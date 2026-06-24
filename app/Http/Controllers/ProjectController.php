<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->projectService->getAllWithSummary()]);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->create($request->validated(), $request->user()->id);
        return response()->json(['data' => $project], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => $this->projectService->getDetail($id)]);
    }

    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        return response()->json(['data' => $this->projectService->update($id, $request->validated())]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->projectService->delete($id);
        return response()->json(['message' => 'Proyek berhasil dihapus.']);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:Draft,Active,Suspended,Completed,Cancelled']]);
        return response()->json(['data' => $this->projectService->updateStatus($id, $request->input('status'))]);
    }
}