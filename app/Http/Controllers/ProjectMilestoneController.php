<?php

namespace App\Http\Controllers;

use App\Http\Requests\Milestone\StoreMilestoneRequest;
use App\Http\Requests\Milestone\UpdateMilestoneRequest;
use App\Services\ProjectMilestoneService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectMilestoneController extends Controller
{
    public function __construct(
        private readonly ProjectMilestoneService $milestoneService
    ) {}

    public function index(int $projectId, Request $request): JsonResponse
    {
        $milestones = $this->milestoneService->getByProject($projectId, $request->query('status'));
        return response()->json(['data' => $milestones]);
    }

    public function store(StoreMilestoneRequest $request, int $projectId): JsonResponse
    {
        $milestone = $this->milestoneService->create($projectId, $request->validated());
        return response()->json(['data' => $milestone], 201);
    }

    public function update(UpdateMilestoneRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->milestoneService->update($id, $request->validated())]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->milestoneService->delete($id);
        return response()->json(['message' => 'Milestone berhasil dihapus.']);
    }

    public function delayed(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->milestoneService->getDelayed($projectId)]);
    }

    public function achieve(int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->milestoneService->achieve($id)]);
    }
}