<?php

namespace App\Http\Controllers;

use App\Http\Requests\Budget\StoreBudgetRequest;
use App\Http\Requests\Budget\UpdateBudgetRequest;
use App\Services\ProjectBudgetService;
use Illuminate\Http\JsonResponse;

class ProjectBudgetController extends Controller
{
    public function __construct(
        private readonly ProjectBudgetService $budgetService
    ) {}

    public function index(int $projectId): JsonResponse
    {
        return response()->json([
            'data'    => $this->budgetService->getByProject($projectId),
            'summary' => $this->budgetService->getSummary($projectId),
        ]);
    }

    public function store(StoreBudgetRequest $request, int $projectId): JsonResponse
    {
        $budget = $this->budgetService->create($projectId, $request->validated());
        return response()->json(['data' => $budget], 201);
    }

    public function update(UpdateBudgetRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->budgetService->update($id, $request->validated())]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->budgetService->delete($id);
        return response()->json(['message' => 'Budget item berhasil dihapus.']);
    }
}