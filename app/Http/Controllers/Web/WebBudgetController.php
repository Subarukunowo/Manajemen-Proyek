<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectBudget;
use App\Services\ProjectBudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebBudgetController extends Controller
{
    use LoadsProjectForSidebar;

    public function __construct(
        private readonly ProjectBudgetService $budgetService,
    ) {}

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $budgets = $this->budgetService->getByProject($project->id);
        $summary = $this->budgetService->getSummary($project->id);

        return view('projects.budget.index', compact('project', 'budgets', 'summary'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'kategori'   => ['required', 'string', 'max:255'],
            'anggaran'   => ['nullable', 'numeric', 'min:0'],
            'realisasi'  => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['anggaran']  = $data['anggaran']  ?? 0;
        $data['realisasi'] = $data['realisasi'] ?? 0;
        $this->budgetService->create($project->id, $data);

        return redirect()->route('projects.budget.index', $project)
            ->with('success', 'Pos anggaran berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectBudget $budget): RedirectResponse
    {
        $data = $request->validate([
            'kategori'   => ['required', 'string', 'max:255'],
            'anggaran'   => ['nullable', 'numeric', 'min:0'],
            'realisasi'  => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $this->budgetService->update($budget->id, $data);

        return redirect()->route('projects.budget.index', $project)
            ->with('success', 'Pos anggaran berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectBudget $budget): RedirectResponse
    {
        $this->budgetService->delete($budget->id);

        return redirect()->route('projects.budget.index', $project)
            ->with('success', 'Pos anggaran berhasil dihapus.');
    }
}
