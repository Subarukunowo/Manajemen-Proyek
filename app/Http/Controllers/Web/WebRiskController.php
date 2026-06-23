<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectRisk;
use App\Services\ProjectRiskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebRiskController extends Controller
{
    use LoadsProjectForSidebar;

    public function __construct(
        private readonly ProjectRiskService $riskService,
    ) {}

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $risks   = $this->riskService->getByProject($project->id);
        $members = $project->members()->with('user')->get();
        return view('projects.risks.index', compact('project', 'risks', 'members'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'deskripsi_risiko' => ['required', 'string'],
            'probabilitas'     => ['required', 'in:Low,Medium,High'],
            'dampak'           => ['required', 'in:Low,Medium,High'],
            'mitigasi'         => ['nullable', 'string'],
            'status'           => ['required', 'in:Identified,Mitigated,Occurred,Closed'],
            'assigned_to'      => ['nullable', 'exists:users,id'],
        ]);
        $this->riskService->create($project->id, $data);

        return redirect()->route('projects.risks.index', $project)
            ->with('success', 'Risiko berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectRisk $risk): RedirectResponse
    {
        $data = $request->validate([
            'deskripsi_risiko' => ['required', 'string'],
            'probabilitas'     => ['required', 'in:Low,Medium,High'],
            'dampak'           => ['required', 'in:Low,Medium,High'],
            'mitigasi'         => ['nullable', 'string'],
            'status'           => ['required', 'in:Identified,Mitigated,Occurred,Closed'],
            'assigned_to'      => ['nullable', 'exists:users,id'],
        ]);
        $this->riskService->update($risk->id, $data);

        return redirect()->route('projects.risks.index', $project)
            ->with('success', 'Risiko berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectRisk $risk): RedirectResponse
    {
        $this->riskService->delete($risk->id);

        return redirect()->route('projects.risks.index', $project)
            ->with('success', 'Risiko berhasil dihapus.');
    }
}
