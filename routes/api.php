<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTokenController;
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
| API Routes — Manajemen Proyek
|--------------------------------------------------------------------------
|
| Semua endpoint dilindungi Sanctum Bearer Token.
| Endpoint tidak ditampilkan di UI. Akses hanya untuk tim internal.
| Dokumentasi: API_SECURITY.md
|
*/

// ── Auth: issue & revoke token (rate limited 10/menit anti brute-force) ──
Route::middleware('throttle:10,1')->group(function () {
    Route::post('auth/token',   [ApiTokenController::class, 'issue']) ->name('api.auth.token');
    Route::delete('auth/token', [ApiTokenController::class, 'revoke'])->name('api.auth.revoke')
        ->middleware('auth:sanctum');
});

// ── Protected API (Sanctum + rate limit 60 req/menit) ────────────────────
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // Projects
    Route::apiResource('projects', ProjectController::class)->names([
        'index'   => 'api.projects.index',
        'store'   => 'api.projects.store',
        'show'    => 'api.projects.show',
        'update'  => 'api.projects.update',
        'destroy' => 'api.projects.destroy',
    ]);
    Route::patch('projects/{id}/status', [ProjectController::class, 'updateStatus'])
        ->name('api.projects.updateStatus');

    // Per-project nested resources
    Route::prefix('projects/{projectId}')->name('api.projects.')->group(function () {

        // Phases
        Route::get('phases/reorder', [ProjectPhaseController::class, 'reorder']);
        Route::apiResource('phases', ProjectPhaseController::class)->except(['show']);

        // Tasks
        Route::get('tasks/tree',              [ProjectTaskController::class, 'tree']);
        Route::get('tasks/{phaseId}/by-phase',[ProjectTaskController::class, 'byPhase']);
        Route::patch('tasks/{id}/progress',   [ProjectTaskController::class, 'updateProgress']);
        Route::apiResource('tasks', ProjectTaskController::class)->except(['show']);

        // Budget
        Route::apiResource('budget', ProjectBudgetController::class)->except(['show']);

        // Risks
        Route::get('risks/critical',          [ProjectRiskController::class, 'critical']);
        Route::patch('risks/{id}/status',     [ProjectRiskController::class, 'updateStatus']);
        Route::apiResource('risks', ProjectRiskController::class)->except(['show']);

        // Change Requests
        Route::patch('changes/{id}/approve',  [ProjectChangeController::class, 'approve']);
        Route::patch('changes/{id}/reject',   [ProjectChangeController::class, 'reject']);
        Route::apiResource('changes', ProjectChangeController::class)->except(['show']);

        // Milestones
        Route::get('milestones/delayed',      [ProjectMilestoneController::class, 'delayed']);
        Route::patch('milestones/{id}/achieve',[ProjectMilestoneController::class, 'achieve']);
        Route::apiResource('milestones', ProjectMilestoneController::class)->except(['show']);

        // Outsourcing
        Route::patch('outsourcing/{id}/progress', [ProjectOutsourcingController::class, 'updateProgress']);
        Route::patch('outsourcing/{id}/terminate',[ProjectOutsourcingController::class, 'terminate']);
        Route::apiResource('outsourcing', ProjectOutsourcingController::class)->except(['show']);

        // Kurva-S
        Route::get('kurva-s',          [ProjectKurvaSController::class, 'index']);
        Route::get('kurva-s/chart',    [ProjectKurvaSController::class, 'chartData']);
        Route::get('kurva-s/latest',   [ProjectKurvaSController::class, 'latest']);
        Route::post('kurva-s',         [ProjectKurvaSController::class, 'store']);
        Route::post('kurva-s/snapshot',[ProjectKurvaSController::class, 'snapshot']);
        Route::delete('kurva-s/{id}',  [ProjectKurvaSController::class, 'destroy']);
    });
});
