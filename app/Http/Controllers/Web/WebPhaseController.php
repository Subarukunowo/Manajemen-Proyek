<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Services\ProjectPhaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebPhaseController extends Controller
{
    use LoadsProjectForSidebar;

    public function __construct(
        private readonly ProjectPhaseService $phaseService,
    ) {}

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $phases = $this->phaseService->getByProject($project->id);
        $phases->load('tasks');
        return view('projects.phases.index', compact('project', 'phases'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:255'],
            'urutan'          => ['nullable', 'integer', 'min:1'],
            'tanggal_mulai'   => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'status'          => ['required', 'in:Pending,On_Progress,Completed'],
        ]);

        $this->phaseService->create($project->id, $data);

        return redirect()->route('projects.phases.index', $project)
            ->with('success', 'Fase berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectPhase $phase): RedirectResponse
    {
        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:255'],
            'urutan'          => ['nullable', 'integer', 'min:1'],
            'tanggal_mulai'   => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'status'          => ['required', 'in:Pending,On_Progress,Completed'],
        ]);

        $this->phaseService->update($phase->id, $data);

        return redirect()->route('projects.phases.index', $project)
            ->with('success', 'Fase berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectPhase $phase): RedirectResponse
    {
        $this->phaseService->delete($phase->id);

        return redirect()->route('projects.phases.index', $project)
            ->with('success', 'Fase berhasil dihapus.');
    }
}
