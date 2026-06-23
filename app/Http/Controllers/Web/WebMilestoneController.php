<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebMilestoneController extends Controller
{
    use LoadsProjectForSidebar;

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $milestones = $project->milestones()->orderBy('tanggal_target')->get();
        return view('projects.milestones.index', compact('project', 'milestones'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'tanggal_target' => ['required', 'date'],
            'tanggal_aktual' => ['nullable', 'date'],
            'status'         => ['required', 'in:Pending,Achieved,Delayed'],
        ]);
        $project->milestones()->create($data);

        return redirect()->route('projects.milestones.index', $project)
            ->with('success', 'Milestone berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        $data = $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'tanggal_target' => ['required', 'date'],
            'tanggal_aktual' => ['nullable', 'date'],
            'status'         => ['required', 'in:Pending,Achieved,Delayed'],
        ]);
        $milestone->update($data);

        return redirect()->route('projects.milestones.index', $project)
            ->with('success', 'Milestone berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        $milestone->delete();

        return redirect()->route('projects.milestones.index', $project)
            ->with('success', 'Milestone berhasil dihapus.');
    }
}
