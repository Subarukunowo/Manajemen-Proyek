<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectRisk;
use App\Models\ProjectMilestone;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $projects = Project::withCount(['tasks', 'risks', 'changes', 'milestones', 'members'])
            ->with(['tasks', 'risks', 'milestones', 'budgetBreakdowns', 'sCurveRecords'])
            ->get();

        // ── Global stats ──────────────────────────────────────
        $stats = [
            'total'         => $projects->count(),
            'active'        => $projects->where('status', 'Active')->count(),
            'completed'     => $projects->where('status', 'Completed')->count(),
            'draft'         => $projects->where('status', 'Draft')->count(),
            'total_budget'  => $projects->sum('anggaran'),
            'total_realisasi' => $projects->sum(fn($p) =>
                $p->budgetBreakdowns->sum('realisasi')
            ),
        ];

        // ── Task aggregates ───────────────────────────────────
        $allTasks = ProjectTask::all();
        $taskStats = [
            'total'       => $allTasks->count(),
            'done'        => $allTasks->where('status', 'Done')->count(),
            'in_progress' => $allTasks->where('status', 'In_Progress')->count(),
            'blocked'     => $allTasks->where('status', 'Blocked')->count(),
            'todo'        => $allTasks->where('status', 'Todo')->count(),
            'high'        => $allTasks->where('prioritas', 'High')->count(),
            'critical'    => $allTasks->where('prioritas', 'Critical')->count(),
        ];

        // ── Risk aggregates ───────────────────────────────────
        $allRisks = ProjectRisk::all();
        $riskStats = [
            'total'     => $allRisks->count(),
            'open'      => $allRisks->whereNotIn('status', ['Mitigated','Closed'])->count(),
            'critical'  => $allRisks->where('skor_risiko', '>=', 6)->whereNotIn('status',['Mitigated','Closed'])->count(),
            'mitigated' => $allRisks->where('status', 'Mitigated')->count(),
        ];

        // ── Milestone aggregates ──────────────────────────────
        $allMilestones = ProjectMilestone::all();
        $milestoneStats = [
            'total'    => $allMilestones->count(),
            'achieved' => $allMilestones->where('status', 'Achieved')->count(),
            'delayed'  => $allMilestones->where('status', 'Delayed')->count(),
            'pending'  => $allMilestones->where('status', 'Pending')->count(),
        ];

        // ── Budget utilisation % ──────────────────────────────
        $budgetPct = $stats['total_budget'] > 0
            ? round($stats['total_realisasi'] / $stats['total_budget'] * 100, 1)
            : 0;

        // ── Recent 5 projects ─────────────────────────────────
        $recentProjects = $projects->sortByDesc('created_at')->take(5);

        // ── Project status distribution for chart ─────────────
        $statusDist = [
            'labels' => ['Active','Completed','Draft','Suspended','Cancelled'],
            'data'   => [
                $projects->where('status','Active')->count(),
                $projects->where('status','Completed')->count(),
                $projects->where('status','Draft')->count(),
                $projects->where('status','Suspended')->count(),
                $projects->where('status','Cancelled')->count(),
            ],
        ];

        return view('dashboard', compact(
            'projects', 'stats', 'taskStats',
            'riskStats', 'milestoneStats', 'budgetPct',
            'recentProjects', 'statusDist'
        ));
    }
}
