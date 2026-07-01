@extends('layouts.app')
@section('title', 'Gantt — '.$project->nama)
@section('topbar-title', 'Gantt Chart Analysis')

@push('styles')
<style>
/* ── Gantt table layout ── */
.gantt-container { display:flex; overflow:hidden; border:1px solid var(--hairline); border-radius:var(--r-lg); background:var(--surface); }

/* Left panel: fixed columns */
.gantt-left { flex-shrink:0; width:520px; border-right:2px solid var(--hairline); overflow:hidden; }
.gantt-left table { width:100%; border-collapse:collapse; font-size:12px; table-layout:fixed; }
.gantt-left thead th {
    background:var(--canvas-soft); color:var(--ink-muted);
    font-size:10px; font-weight:700; letter-spacing:.6px; text-transform:uppercase;
    padding:8px 10px; border-bottom:1px solid var(--hairline);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.gantt-left tbody td { padding:6px 10px; border-bottom:1px solid var(--hairline); vertical-align:middle; }
.gantt-left tbody tr:hover td, .gantt-right tbody tr:hover td { background:#f9f9f8; }
.gantt-left tbody tr:last-child td { border-bottom:none; }

/* WBS column */
td.wbs { font-family:monospace; font-size:11px; color:var(--ink-faint); width:70px; }
td.task-name { font-size:12px; }
td.task-name.is-milestone { color:#0075de; font-weight:600; }
td.task-name.is-task { color:var(--ink-secondary); padding-left:20px; }
td.project-col { font-size:11px; color:var(--ink-muted); width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

/* Progress circle */
.progress-circle {
    width:28px; height:28px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:9px; font-weight:700; flex-shrink:0;
}
.p-done    { background:#dcfce7; color:#15803d; border:2px solid #86efac; }
.p-high    { background:#fee2e2; color:#b91c1c; border:2px solid #fca5a5; }
.p-mid     { background:#fef9c3; color:#a16207; border:2px solid #fde047; }
.p-low     { background:#f3f4f6; color:#6b7280; border:2px solid #d1d5db; }

/* Right panel: scrollable timeline */
.gantt-right { flex:1; overflow-x:auto; }
.gantt-right table { border-collapse:collapse; font-size:11px; white-space:nowrap; }
.gantt-right thead th {
    background:var(--canvas-soft); color:var(--ink-muted);
    font-size:9px; font-weight:700; letter-spacing:.5px; text-transform:uppercase;
    padding:8px 4px; border-bottom:1px solid var(--hairline);
    text-align:center; min-width:52px;
}
.gantt-right thead th.today-col { background:#dbeafe; color:#1d4ed8; }
.gantt-right tbody td { padding:6px 4px; border-bottom:1px solid var(--hairline); text-align:center; height:38px; position:relative; }
.gantt-right tbody tr:last-child td { border-bottom:none; }
.gantt-right tbody td.today-col { background:rgba(0,117,222,.04); }

/* Bar inside cell */
.gantt-bar {
    height:14px; border-radius:3px; display:block;
    position:absolute; top:50%; transform:translateY(-50%);
}
.bar-plan     { background:#0075de; opacity:.85; }
.bar-done     { background:#1aae39; }
.bar-progress { background:#f59e0b; }
.bar-blocked  { background:#ef4444; opacity:.8; }
.bar-milestone{ background:#7e22ce; width:12px !important; height:12px; border-radius:2px; transform:translateY(-50%) rotate(45deg); }

/* Today line */
.today-line { position:absolute; top:0; left:50%; width:2px; height:100%; background:#0075de; opacity:.3; pointer-events:none; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none">
            <i class="fas fa-arrow-left"></i> {{ $project->nama }}
        </a>
        <h1 class="page-title" style="margin-top:4px">Gantt Chart</h1>
        <p class="page-desc">Visualisasi jadwal task dan milestone.</p>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
        <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-utility btn-sm">
            <i class="fas fa-list-check"></i> Kelola Task
        </a>
    </div>
</div>

{{-- Filter fase --}}
<div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;align-items:center">
    <span style="font-size:12px;color:var(--ink-muted)">Filter fase:</span>
    <a href="{{ route('projects.gantt.index', $project) }}"
       class="btn btn-utility btn-sm"
       style="{{ !request('phase') ? 'background:var(--primary);color:#fff;border-color:var(--primary)' : '' }}">
       Semua
    </a>
    @foreach($phases as $ph)
    <a href="{{ route('projects.gantt.index', [$project,'phase'=>$ph->id]) }}"
       class="btn btn-utility btn-sm"
       style="{{ request('phase')==$ph->id ? 'background:var(--primary);color:#fff;border-color:var(--primary)' : '' }}">
       {{ $ph->nama }}
    </a>
    @endforeach
</div>

@if($tasks->isEmpty())
<div class="empty-state">
    <div class="empty-icon"><i class="fas fa-bars-progress"></i></div>
    <p>Belum ada task dengan tanggal lengkap untuk ditampilkan.</p>
    <div style="margin-top:16px">
        <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-primary btn-sm">Tambah Task</a>
    </div>
</div>
@else

@php
// ── Build timeline columns (weeks) ──────────────────────────────
$allDates = $tasks->flatMap(fn($t) => [$t->tanggal_mulai, $t->tanggal_selesai])->filter();
$minDate  = $allDates->min()->copy()->startOfWeek();
$maxDate  = $allDates->max()->copy()->endOfWeek()->addWeeks(1);
$today    = now()->startOfDay();

$weeks = [];
$cur   = $minDate->copy();
$wNum  = 1;
while ($cur->lte($maxDate)) {
    $weeks[] = ['label' => 'd/m' === 'd/m' ? $cur->format('d M') : 'W'.$wNum, 'start' => $cur->copy(), 'end' => $cur->copy()->endOfWeek(), 'is_today' => $today->between($cur, $cur->copy()->endOfWeek())];
    $cur->addWeek();
    $wNum++;
}

// ── Build rows (phases → tasks) ──────────────────────────────────
$rows = [];
foreach ($phases->sortBy('urutan') as $ph) {
    $phaseTasks = $tasks->where('phase_id', $ph->id)->sortBy('tanggal_mulai');
    if ($phaseTasks->isEmpty()) continue;
    // Phase header row
    $rows[] = ['type'=>'phase','label'=>$ph->nama,'wbs'=>'P'.$ph->urutan,'start'=>$phaseTasks->min('tanggal_mulai'),'end'=>$phaseTasks->max('tanggal_selesai'),'progress'=>$ph->tasks->avg('persen_selesai') ?? 0,'status'=>$ph->status,'project'=>$project->kode];
    foreach ($phaseTasks as $t) {
        $rows[] = ['type'=>'task','label'=>$t->nama,'wbs'=>'T'.$t->id,'start'=>$t->tanggal_mulai,'end'=>$t->tanggal_selesai,'progress'=>$t->persen_selesai,'status'=>$t->status,'project'=>$project->kode,'prioritas'=>$t->prioritas];
    }
}
// Tasks without phase
$noPhase = $tasks->whereNull('phase_id')->sortBy('tanggal_mulai');
foreach ($noPhase as $t) {
    $rows[] = ['type'=>'task','label'=>$t->nama,'wbs'=>'T'.$t->id,'start'=>$t->tanggal_mulai,'end'=>$t->tanggal_selesai,'progress'=>$t->persen_selesai,'status'=>$t->status,'project'=>$project->kode,'prioritas'=>$t->prioritas];
}
@endphp

{{-- Legend --}}
<div style="display:flex;gap:16px;margin-bottom:12px;flex-wrap:wrap;align-items:center;font-size:12px;color:var(--ink-muted)">
    <span style="display:flex;align-items:center;gap:5px"><span style="width:16px;height:10px;border-radius:2px;background:#0075de;display:inline-block"></span> Task</span>
    <span style="display:flex;align-items:center;gap:5px"><span style="width:16px;height:10px;border-radius:2px;background:#1aae39;display:inline-block"></span> Done</span>
    <span style="display:flex;align-items:center;gap:5px"><span style="width:16px;height:10px;border-radius:2px;background:#f59e0b;display:inline-block"></span> In Progress</span>
    <span style="display:flex;align-items:center;gap:5px"><span style="width:16px;height:10px;border-radius:2px;background:#ef4444;display:inline-block"></span> Blocked</span>
    <span style="display:flex;align-items:center;gap:5px"><span style="width:12px;height:12px;border-radius:2px;background:#7e22ce;display:inline-block;transform:rotate(45deg)"></span> Fase</span>
    <span style="display:flex;align-items:center;gap:5px;margin-left:8px"><span style="width:2px;height:14px;background:#0075de;opacity:.5;display:inline-block"></span> Hari ini</span>
</div>

<div class="gantt-container">
    {{-- ── Left fixed columns ── --}}
    <div class="gantt-left">
        <table>
            <thead>
                <tr>
                    <th style="width:60px">WBS</th>
                    <th>Milestone / Task</th>
                    <th style="width:90px">Proyek</th>
                    <th style="width:70px;text-align:center">Progress</th>
                </tr>
            </thead>
            <tbody>
            @foreach($rows as $row)
            <tr>
                <td class="wbs">{{ $row['wbs'] }}</td>
                <td class="task-name {{ $row['type']==='phase' ? 'is-milestone' : 'is-task' }}">
                    {{ $row['label'] }}
                </td>
                <td class="project-col" title="{{ $row['project'] }}">{{ $row['project'] }}</td>
                <td style="text-align:center">
                    @php
                    $pct = round($row['progress']);
                    $cls = $pct >= 100 ? 'p-done' : ($pct >= 50 ? 'p-mid' : ($pct > 0 ? 'p-high' : 'p-low'));
                    // Reuse p-high as in-progress style for non-zero
                    if ($pct > 0 && $pct < 50) $cls = 'p-high';
                    if ($pct >= 50 && $pct < 100) $cls = 'p-mid';
                    @endphp
                    <span class="progress-circle {{ $cls }}">{{ $pct }}%</span>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── Right scrollable timeline ── --}}
    <div class="gantt-right">
        <table>
            <thead>
                <tr>
                @foreach($weeks as $w)
                <th class="{{ $w['is_today'] ? 'today-col' : '' }}">{{ $w['label'] }}</th>
                @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach($rows as $row)
            <tr>
                @foreach($weeks as $w)
                @php
                $inRange  = $row['start'] && $row['end'] && $row['start']->lte($w['end']) && $row['end']->gte($w['start']);
                $isFirst  = $inRange && $row['start']->between($w['start'], $w['end']);
                $isLast   = $inRange && $row['end']->between($w['start'], $w['end']);
                $barColor = match($row['status'] ?? '') {
                    'Done','Completed' => 'bar-done',
                    'In_Progress','On_Progress' => 'bar-progress',
                    'Blocked' => 'bar-blocked',
                    default => $row['type']==='phase' ? 'bar-milestone' : 'bar-plan',
                };

                // Calculate left% and width% within this week cell
                $cellStart = $w['start'];
                $cellEnd   = $w['end'];
                $cellDays  = $cellStart->diffInDays($cellEnd) + 1;

                $barStart  = $row['start'] && $row['start']->gt($cellStart) ? $row['start'] : $cellStart;
                $barEnd    = $row['end'] && $row['end']->lt($cellEnd) ? $row['end'] : $cellEnd;
                $leftPct   = $inRange ? round($cellStart->diffInDays($barStart) / $cellDays * 100) : 0;
                $widthPct  = $inRange ? max(8, round($barStart->diffInDays($barEnd) / $cellDays * 100 + ($cellDays > 1 ? 100/$cellDays : 100))) : 0;
                @endphp
                <td class="{{ $w['is_today'] ? 'today-col' : '' }}" style="position:relative">
                    @if($w['is_today'])<div class="today-line"></div>@endif
                    @if($inRange)
                    <span class="gantt-bar {{ $barColor }}"
                          style="left:{{ $leftPct }}%;width:{{ min($widthPct, 100 - $leftPct) }}%"
                          title="{{ $row['label'] }} — {{ $row['progress'] }}%"></span>
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div style="font-size:12px;color:var(--ink-faint);margin-top:8px;text-align:right">
    {{ count($rows) }} baris · {{ count($weeks) }} minggu ditampilkan
</div>
@endif
@endsection

@push('scripts')
<script>
// Sync scroll left/right panel rows vertically
document.addEventListener('DOMContentLoaded', () => {
    const left  = document.querySelector('.gantt-left');
    const right = document.querySelector('.gantt-right');
    if (!left || !right) return;

    // Sync left tbody height to right tbody
    const syncHeights = () => {
        const leftRows  = left.querySelectorAll('tbody tr');
        const rightRows = right.querySelectorAll('tbody tr');
        leftRows.forEach((row, i) => {
            if (rightRows[i]) {
                const h = Math.max(row.offsetHeight, rightRows[i].offsetHeight);
                row.style.height = h + 'px';
                rightRows[i].style.height = h + 'px';
            }
        });
    };
    syncHeights();
    window.addEventListener('resize', syncHeights);
});
</script>
@endpush
