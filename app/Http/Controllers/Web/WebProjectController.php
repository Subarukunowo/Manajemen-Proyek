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
        // Hanya admin yang bisa membuat proyek baru
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat membuat proyek baru.');
        }
        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat membuat proyek baru.');
        }
        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'status'          => ['required', 'in:Draft,Active,Suspended,Completed,Cancelled'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'anggaran'        => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $project = $this->projectService->create($data, auth()->id());

            // Otomatis set creator sebagai Owner
            try {
                $project->members()->create([
                    'user_id'           => auth()->id(),
                    'peran'             => 'Owner',
                    'tanggal_bergabung' => now()->toDateString(),
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Member create skipped: ' . $e->getMessage());
            }

            session(['sidebar_project_id' => $project->id]);

            return redirect()->route('projects.show', $project)
                ->with('success', 'Proyek ' . $project->kode . ' berhasil dibuat!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Project store failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['general' => 'Gagal menyimpan proyek: ' . $e->getMessage()]);
        }
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
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengedit proyek.');
        }
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengedit proyek.');
        }
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
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat menghapus proyek.');
        }
        $this->projectService->delete($project->id);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }
}
