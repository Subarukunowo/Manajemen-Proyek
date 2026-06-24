<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskProgressRequest;
use App\Services\ProjectTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function __construct(
        private readonly ProjectTaskService $taskService
    ) {}

    public function index(int $projectId, Request $request): JsonResponse
    {
        $tasks = $this->taskService->getByProject($projectId, $request->only(['status', 'prioritas', 'assigned_to']));
        return response()->json(['data' => $tasks]);
    }

    public function store(StoreTaskRequest $request, int $projectId): JsonResponse
    {
        $task = $this->taskService->create($projectId, $request->validated());
        return response()->json(['data' => $task], 201);
    }

    public function update(UpdateTaskRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->taskService->update($id, $request->validated())]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->taskService->delete($id);
        return response()->json(['message' => 'Task berhasil dihapus.']);
    }

    public function tree(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->taskService->getRootTasks($projectId)]);
    }

    public function byPhase(int $projectId, int $phaseId): JsonResponse
    {
        return response()->json(['data' => $this->taskService->getByPhase($phaseId)]);
    }

    public function updateProgress(UpdateTaskProgressRequest $request, int $projectId, int $id): JsonResponse
    {
        $task = $this->taskService->updateProgress($id, $request->input('persen_selesai'));
        return response()->json(['data' => $task]);
    }
}