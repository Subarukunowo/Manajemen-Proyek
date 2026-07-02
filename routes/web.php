<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\WebProjectController;
use App\Http\Controllers\Web\WebPhaseController;
use App\Http\Controllers\Web\WebTaskController;
use App\Http\Controllers\Web\WebBudgetController;
use App\Http\Controllers\Web\WebRiskController;
use App\Http\Controllers\Web\WebChangeController;
use App\Http\Controllers\Web\WebKurvaSController;
use App\Http\Controllers\Web\WebMilestoneController;
use App\Http\Controllers\Web\WebMemberController;
use App\Http\Controllers\Web\WebResourceController;
use App\Http\Controllers\Web\WebOutsourcingController;

// ── Auth (guest only) ─────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Google OAuth — jalur utama
    Route::get('/auth/google',          [AuthController::class, 'googleRedirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('google.callback');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', WebProjectController::class);

    Route::prefix('projects/{project}')->name('projects.')->group(function () {

        // Phases
        Route::get('phases',            [WebPhaseController::class,   'index'])  ->name('phases.index');
        Route::post('phases',           [WebPhaseController::class,   'store'])  ->name('phases.store');
        Route::put('phases/{phase}',    [WebPhaseController::class,   'update']) ->name('phases.update');
        Route::delete('phases/{phase}', [WebPhaseController::class,   'destroy'])->name('phases.destroy');

        // Tasks
        Route::get('tasks',             [WebTaskController::class,    'index'])  ->name('tasks.index');
        Route::post('tasks',            [WebTaskController::class,    'store'])  ->name('tasks.store');
        Route::put('tasks/{task}',      [WebTaskController::class,    'update']) ->name('tasks.update');
        Route::delete('tasks/{task}',   [WebTaskController::class,    'destroy'])->name('tasks.destroy');

        // Gantt (read-only view, same task data)
        Route::get('gantt', [WebTaskController::class, 'gantt'])->name('gantt.index');

        // Budget
        Route::get('budget',            [WebBudgetController::class,  'index'])  ->name('budget.index');
        Route::post('budget',           [WebBudgetController::class,  'store'])  ->name('budget.store');
        Route::put('budget/{budget}',   [WebBudgetController::class,  'update']) ->name('budget.update');
        Route::delete('budget/{budget}',[WebBudgetController::class,  'destroy'])->name('budget.destroy');

        // Risks
        Route::get('risks',             [WebRiskController::class,    'index'])  ->name('risks.index');
        Route::post('risks',            [WebRiskController::class,    'store'])  ->name('risks.store');
        Route::put('risks/{risk}',      [WebRiskController::class,    'update']) ->name('risks.update');
        Route::delete('risks/{risk}',   [WebRiskController::class,    'destroy'])->name('risks.destroy');

        // Changes
        Route::get('changes',                    [WebChangeController::class, 'index'])  ->name('changes.index');
        Route::post('changes',                   [WebChangeController::class, 'store'])  ->name('changes.store');
        Route::put('changes/{change}',           [WebChangeController::class, 'update']) ->name('changes.update');
        Route::patch('changes/{change}/approve', [WebChangeController::class, 'approve'])->name('changes.approve');
        Route::patch('changes/{change}/reject',  [WebChangeController::class, 'reject']) ->name('changes.reject');
        Route::delete('changes/{change}',        [WebChangeController::class, 'destroy'])->name('changes.destroy');

        // Kurva-S
        Route::get('kurvas',              [WebKurvaSController::class, 'index'])   ->name('kurvas.index');
        Route::post('kurvas',             [WebKurvaSController::class, 'store'])   ->name('kurvas.store');
        Route::put('kurvas/{kurvaS}',     [WebKurvaSController::class, 'update'])  ->name('kurvas.update');
        Route::post('kurvas/snapshot',    [WebKurvaSController::class, 'snapshot'])->name('kurvas.snapshot');
        Route::delete('kurvas/{kurvaS}',  [WebKurvaSController::class, 'destroy']) ->name('kurvas.destroy');

        // Milestones
        Route::get('milestones',                       [WebMilestoneController::class, 'index'])  ->name('milestones.index');
        Route::post('milestones',                      [WebMilestoneController::class, 'store'])  ->name('milestones.store');
        Route::put('milestones/{milestone}',           [WebMilestoneController::class, 'update']) ->name('milestones.update');
        Route::delete('milestones/{milestone}',        [WebMilestoneController::class, 'destroy'])->name('milestones.destroy');

        // Members
        Route::get('members',                   [WebMemberController::class, 'index'])  ->name('members.index');
        Route::post('members',                  [WebMemberController::class, 'store'])  ->name('members.store');
        Route::put('members/{member}',          [WebMemberController::class, 'update']) ->name('members.update');
        Route::delete('members/{member}',       [WebMemberController::class, 'destroy'])->name('members.destroy');

        // Resources
        Route::get('resources',                 [WebResourceController::class,    'index'])  ->name('resources.index');
        Route::post('resources',                [WebResourceController::class,    'store'])  ->name('resources.store');
        Route::put('resources/{resource}',      [WebResourceController::class,    'update']) ->name('resources.update');
        Route::delete('resources/{resource}',   [WebResourceController::class,    'destroy'])->name('resources.destroy');

        // Outsourcing / Vendor
        Route::get('outsourcing',                       [WebOutsourcingController::class, 'index'])  ->name('outsourcing.index');
        Route::post('outsourcing',                      [WebOutsourcingController::class, 'store'])  ->name('outsourcing.store');
        Route::put('outsourcing/{outsourcing}',         [WebOutsourcingController::class, 'update']) ->name('outsourcing.update');
        Route::delete('outsourcing/{outsourcing}',      [WebOutsourcingController::class, 'destroy'])->name('outsourcing.destroy');
    });
});
