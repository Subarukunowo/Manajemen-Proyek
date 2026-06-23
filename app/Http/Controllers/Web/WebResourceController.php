<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebResourceController extends Controller
{
    use LoadsProjectForSidebar;

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $resources = $project->resources()->with('task')->orderBy('tipe')->get();
        $tasks     = $project->tasks()->orderBy('nama')->get();

        $summary = [
            'total_biaya' => $resources->sum('total_biaya'),
            'by_tipe'     => $resources->groupBy('tipe')->map(fn ($g) => [
                'count'       => $g->count(),
                'total_biaya' => $g->sum('total_biaya'),
            ]),
        ];

        return view('projects.resources.index', compact('project', 'resources', 'tasks', 'summary'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'nama_resource' => ['required', 'string', 'max:255'],
            'tipe'          => ['required', 'in:Human,Material,Equipment'],
            'task_id'       => ['nullable', 'exists:project_tasks,id'],
            'jumlah'        => ['required', 'numeric', 'min:0'],
            'satuan'        => ['required', 'string', 'max:50'],
            'biaya_satuan'  => ['required', 'numeric', 'min:0'],
        ]);
        $project->resources()->create($data);

        return redirect()->route('projects.resources.index', $project)
            ->with('success', 'Resource berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectResource $resource): RedirectResponse
    {
        $data = $request->validate([
            'nama_resource' => ['required', 'string', 'max:255'],
            'tipe'          => ['required', 'in:Human,Material,Equipment'],
            'task_id'       => ['nullable', 'exists:project_tasks,id'],
            'jumlah'        => ['required', 'numeric', 'min:0'],
            'satuan'        => ['required', 'string', 'max:50'],
            'biaya_satuan'  => ['required', 'numeric', 'min:0'],
        ]);
        $resource->update($data);

        return redirect()->route('projects.resources.index', $project)
            ->with('success', 'Resource berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectResource $resource): RedirectResponse
    {
        $resource->delete();

        return redirect()->route('projects.resources.index', $project)
            ->with('success', 'Resource berhasil dihapus.');
    }
}
