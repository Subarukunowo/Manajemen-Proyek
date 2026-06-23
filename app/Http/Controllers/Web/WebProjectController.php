<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
    ) {}

    public function index(Request $request): View
    {
        $projects = $this->projectService->getAllWithSummary();

        if ($status = $request->query('status')) {
            $projects = $projects->where('status', $status)->values();
        }

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'status'          => ['required', 'in:Draft,Active,Suspended,Completed,Cancelled'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'anggaran'        => ['required', 'numeric', 'min:0'],
        ]);

        $this->projectService->create($data, auth()->id());

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dibuat.');
    }

    public function show(Project $project): View
    {
        $project->load([
            'phases.tasks',
            'tasks:id,project_id,status',
            'members.user',
            'risks:id,project_id,status',
            'changes:id,project_id,status',
            'milestones',
            'budgetBreakdowns',
        ]);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'status'          => ['required', 'in:Draft,Active,Suspended,Completed,Cancelled'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'anggaran'        => ['required', 'numeric', 'min:0'],
        ]);

        $this->projectService->update($project->id, $data);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->projectService->delete($project->id);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }
}
