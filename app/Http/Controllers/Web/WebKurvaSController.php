<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectKurvaS;
use App\Services\ProjectKurvaSService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebKurvaSController extends Controller
{
    use LoadsProjectForSidebar;

    public function __construct(
        private readonly ProjectKurvaSService $kurvaSService,
    ) {}

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $records   = $this->kurvaSService->getByProject($project->id);
        $latest    = $this->kurvaSService->getLatest($project->id);
        $chartData = $this->kurvaSService->getChartData($project->id);

        return view('projects.kurvas.index', compact('project', 'records', 'latest', 'chartData'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'periode'             => ['required', 'date'],
            'rencana_kumulatif'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'realisasi_kumulatif' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rencana_periode'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'realisasi_periode'   => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
        $this->kurvaSService->upsert($project->id, $data['periode'], $data);

        return redirect()->route('projects.kurvas.index', $project)
            ->with('success', 'Data Kurva-S berhasil disimpan.');
    }

    public function update(Request $request, Project $project, ProjectKurvaS $kurvaS): RedirectResponse
    {
        $data = $request->validate([
            'periode'             => ['required', 'date'],
            'rencana_kumulatif'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'realisasi_kumulatif' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rencana_periode'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'realisasi_periode'   => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
        $kurvaS->update($data);

        return redirect()->route('projects.kurvas.index', $project)
            ->with('success', 'Data Kurva-S berhasil diperbarui.');
    }

    public function snapshot(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'periode'           => ['required', 'date'],
            'rencana_kumulatif' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
        $this->kurvaSService->takeAutoSnapshot(
            $project->id,
            $data['periode'],
            (float) $data['rencana_kumulatif']
        );

        return redirect()->route('projects.kurvas.index', $project)
            ->with('success', 'Snapshot Kurva-S berhasil diambil.');
    }

    public function destroy(Project $project, ProjectKurvaS $kurvaS): RedirectResponse
    {
        $this->kurvaSService->delete($kurvaS->id);

        return redirect()->route('projects.kurvas.index', $project)
            ->with('success', 'Data Kurva-S berhasil dihapus.');
    }
}
