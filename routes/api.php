<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPhaseController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ProjectBudgetController;
use App\Http\Controllers\ProjectRiskController;
use App\Http\Controllers\ProjectChangeController;
use App\Http\Controllers\ProjectKurvaSController;
use App\Http\Controllers\ProjectMilestoneController;
use App\Http\Controllers\ProjectOutsourcingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Projects — prefix name dengan 'api.' agar tidak bentrok dengan web routes
    Route::apiResource('projects', ProjectController::class)->names([
        'index'   => 'api.projects.index',
        'store'   => 'api.projects.store',
        'show'    => 'api.projects.show',
        'update'  => 'api.projects.update',
        'destroy' => 'api.projects.destroy',
    ]);
    Route::patch('projects/{id}/status', [ProjectController::class, 'updateStatus'])->name('api.projects.updateStatus');

    // Per-project nested resources
    Route::prefix('projects/{projectId}')->name('api.projects.')->group(function () {

        // Phases
        Route::apiResource('phases', ProjectPhaseController::class)->except(['show']);
        Route::patch('phases/reorder', [ProjectPhaseController::class, 'reorder']);

        // Tasks
        Route::apiResource('tasks', ProjectTaskController::class)->except(['show']);
        Route::get('tasks/tree', [ProjectTaskController::class, 'tree']);
        Route::get('phases/{phaseId}/tasks', [ProjectTaskController::class, 'byPhase']);
        Route::patch('tasks/{id}/progress', [ProjectTaskController::class, 'updateProgress']);

        // Budget
        Route::apiResource('budget', ProjectBudgetController::class)->except(['show']);

        // Risks
        Route::apiResource('risks', ProjectRiskController::class)->except(['show']);
        Route::get('risks/critical', [ProjectRiskController::class, 'critical']);
        Route::patch('risks/{id}/status', [ProjectRiskController::class, 'updateStatus']);

        // Change Requests
        Route::apiResource('changes', ProjectChangeController::class)->except(['show']);
        Route::patch('changes/{id}/approve', [ProjectChangeController::class, 'approve']);
        Route::patch('changes/{id}/reject', [ProjectChangeController::class, 'reject']);

        // Milestones
        Route::apiResource('milestones', ProjectMilestoneController::class)->except(['show']);
        Route::get('milestones/delayed', [ProjectMilestoneController::class, 'delayed']);
        Route::patch('milestones/{id}/achieve', [ProjectMilestoneController::class, 'achieve']);

        // Outsourcing
        Route::apiResource('outsourcing', ProjectOutsourcingController::class)->except(['show']);
        Route::patch('outsourcing/{id}/progress', [ProjectOutsourcingController::class, 'updateProgress']);
        Route::patch('outsourcing/{id}/terminate', [ProjectOutsourcingController::class, 'terminate']);

        // Kurva-S
        Route::get('kurva-s', [ProjectKurvaSController::class, 'index']);
        Route::get('kurva-s/chart', [ProjectKurvaSController::class, 'chartData']);
        Route::get('kurva-s/latest', [ProjectKurvaSController::class, 'latest']);
        Route::post('kurva-s', [ProjectKurvaSController::class, 'store']);
        Route::post('kurva-s/snapshot', [ProjectKurvaSController::class, 'snapshot']);
        Route::delete('kurva-s/{id}', [ProjectKurvaSController::class, 'destroy']);
    });
});