<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Services\ProjectPhaseService;
use App\Services\ProjectTaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebTaskController extends Controller
{
    use LoadsProjectForSidebar;

    public function __construct(
        private readonly ProjectTaskService  $taskService,
        private readonly ProjectPhaseService $phaseService,
    ) {}

    public function index(Project $project, Request $request): View
    {
        $this->loadProjectForSidebar($project);
        $filters = $request->only(['status', 'prioritas']);
        $tasks   = $this->taskService->getByProject($project->id, $filters);
        $tasks->load('phase', 'assignee');
        $phases  = $this->phaseService->getByProject($project->id);
        $members = $project->members()->with('user')->get();
        return view('projects.tasks.index', compact('project', 'tasks', 'phases', 'members'));
    }

    public function gantt(Project $project, Request $request): View
    {
        $this->loadProjectForSidebar($project);
        $phases = $this->phaseService->getByProject($project->id);
        $query  = $project->tasks()->whereNotNull('tanggal_mulai')->whereNotNull('tanggal_selesai');
        if ($phaseId = $request->query('phase')) {
            $query->where('phase_id', $phaseId);
        }
        $tasks = $query->orderBy('tanggal_mulai')->get();
        return view('projects.gantt.index', compact('project', 'tasks', 'phases'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'phase_id'        => ['nullable', 'exists:project_phases,id'],
            'parent_task_id'  => ['nullable', 'exists:project_tasks,id'],
            'tanggal_mulai'   => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'durasi_hari'     => ['nullable', 'integer', 'min:0'],
            'prioritas'       => ['required', 'in:Low,Medium,High,Critical'],
            'status'          => ['nullable', 'in:Todo,In_Progress,Blocked,Done'],
            'assigned_to'     => ['nullable', 'exists:users,id'],
        ]);

        $data['status'] = $data['status'] ?? 'Todo';
        $this->taskService->create($project->id, $data);

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Task berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'phase_id'        => ['nullable', 'exists:project_phases,id'],
            'tanggal_mulai'   => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'durasi_hari'     => ['nullable', 'integer', 'min:0'],
            'prioritas'       => ['required', 'in:Low,Medium,High,Critical'],
            'status'          => ['required', 'in:Todo,In_Progress,Blocked,Done'],
            'persen_selesai'  => ['nullable', 'integer', 'min:0', 'max:100'],
            'assigned_to'     => ['nullable', 'exists:users,id'],
        ]);

        $this->taskService->update($task->id, $data);

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Task berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectTask $task): RedirectResponse
    {
        $this->taskService->delete($task->id);

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Task berhasil dihapus.');
    }
}
