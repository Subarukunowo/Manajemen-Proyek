<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectChange;
use App\Services\ProjectChangeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebChangeController extends Controller
{
    use LoadsProjectForSidebar;

    public function __construct(
        private readonly ProjectChangeService $changeService,
    ) {}

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $changes = $this->changeService->getByProject($project->id);
        return view('projects.changes.index', compact('project', 'changes'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'judul'           => ['required', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'dampak'          => ['required', 'in:Low,Medium,High'],
            'biaya_perubahan' => ['nullable', 'numeric', 'min:0'],
        ]);
        $this->changeService->create($project->id, $data, auth()->id());

        return redirect()->route('projects.changes.index', $project)
            ->with('success', 'Change request berhasil diajukan.');
    }

    public function update(Request $request, Project $project, ProjectChange $change): RedirectResponse
    {
        $data = $request->validate([
            'judul'           => ['required', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'dampak'          => ['required', 'in:Low,Medium,High'],
            'status'          => ['nullable', 'in:Draft,Pending,Approved,Rejected'],
            'biaya_perubahan' => ['nullable', 'numeric', 'min:0'],
        ]);
        $this->changeService->update($change->id, $data);

        return redirect()->route('projects.changes.index', $project)
            ->with('success', 'Change request berhasil diperbarui.');
    }

    public function approve(Project $project, ProjectChange $change): RedirectResponse
    {
        $this->changeService->approve($change->id, auth()->id());

        return redirect()->route('projects.changes.index', $project)
            ->with('success', 'Change request disetujui.');
    }

    public function reject(Project $project, ProjectChange $change): RedirectResponse
    {
        $this->changeService->reject($change->id, auth()->id());

        return redirect()->route('projects.changes.index', $project)
            ->with('success', 'Change request ditolak.');
    }

    public function destroy(Project $project, ProjectChange $change): RedirectResponse
    {
        $this->changeService->delete($change->id);

        return redirect()->route('projects.changes.index', $project)
            ->with('success', 'Change request berhasil dihapus.');
    }
}
