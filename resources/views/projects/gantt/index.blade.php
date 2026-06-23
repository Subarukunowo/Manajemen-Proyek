@extends('layouts.app')
@section('title', 'Gantt — '.$project->nama)
@section('topbar-title', 'Gantt Chart')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
<style>
.gantt-wrap { overflow-x: auto; background: var(--surface); border-radius: var(--r-lg); border: 1px solid var(--hairline); padding: var(--sp-md); }
.gantt .grid-header { fill: var(--canvas-soft); }
.gantt .grid-row { fill: transparent; }
.gantt .grid-row:nth-child(even) { fill: #f9f9f8; }
.gantt .tick { stroke: var(--hairline); }
.gantt .today-highlight { fill: rgba(0,117,222,.06); }
.gantt .bar { fill: var(--primary); border-radius: 4px; }
.gantt .bar-progress { fill: var(--primary-active); }
.gantt .bar-label { fill: #fff; font-family: 'Inter', sans-serif; font-size: 11px; }
.gantt .lower-text, .gantt .upper-text { fill: var(--ink-muted); font-family: 'Inter', sans-serif; font-size: 11px; }
/* View mode tabs */
.view-tabs { display:flex; gap:4px; }
.view-tab { padding:5px 12px; border-radius:var(--r-md); border:1px solid var(--hairline); font-size:13px; background:var(--surface); color:var(--ink-muted); cursor:pointer; }
.view-tab.active { background:var(--primary); color:#fff; border-color:var(--primary); }
/* Legend */
.legend { display:flex; gap:var(--sp-md); align-items:center; flex-wrap:wrap; }
.legend-item { display:flex; align-items:center; gap:6px; font-size:13px; color:var(--ink-muted); }
.legend-dot { width:12px; height:12px; border-radius:3px; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:var(--sp-md)">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Gantt Chart</h1>
        <p class="page-desc">Visualisasi jadwal task secara kronologis.</p>
    </div>
    <div style="display:flex;align-items:center;gap:var(--sp-sm)">
        <div class="view-tabs">
            <button class="view-tab active" onclick="setView('Day',this)">Hari</button>
            <button class="view-tab" onclick="setView('Week',this)">Minggu</button>
            <button class="view-tab" onclick="setView('Month',this)">Bulan</button>
        </div>
        <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-utility btn-sm">Kelola Task</a>
    </div>
</div>

{{-- Filter by phase --}}
<div style="display:flex;gap:var(--sp-sm);margin-bottom:var(--sp-md);flex-wrap:wrap;align-items:center">
    <span style="font-size:13px;color:var(--ink-muted)">Filter fase:</span>
    <a href="{{ route('projects.gantt.index', $project) }}"
       class="btn btn-utility btn-sm {{ !request('phase') ? 'btn-primary' : '' }}"
       style="{{ !request('phase') ? 'background:var(--primary);color:#fff;border-color:var(--primary)' : '' }}">Semua</a>
    @foreach($phases as $ph)
    <a href="{{ route('projects.gantt.index', [$project,'phase'=>$ph->id]) }}"
       class="btn btn-utility btn-sm {{ request('phase')==$ph->id ? 'btn-primary' : '' }}"
       style="{{ request('phase')==$ph->id ? 'background:var(--primary);color:#fff;border-color:var(--primary)' : '' }}">
       {{ $ph->nama }}
    </a>
    @endforeach
</div>

@if($tasks->isEmpty())
<div class="empty-state">
    <div class="empty-icon"><i class="fas fa-bars-progress"></i></div>
    <p>Belum ada task dengan tanggal yang lengkap untuk ditampilkan di Gantt Chart.</p>
    <div style="margin-top:var(--sp-md)">
        <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-primary btn-sm">Tambah Task</a>
    </div>
</div>
@else

<div class="legend" style="margin-bottom:var(--sp-md)">
    <div class="legend-item"><div class="legend-dot" style="background:var(--primary)"></div> Task</div>
    <div class="legend-item"><div class="legend-dot" style="background:#0075de88"></div> Progress</div>
    <div class="legend-item"><div class="legend-dot" style="background:#e6e6e6"></div> Hari ini</div>
    @foreach(['Todo'=>'#a39e98','In_Progress'=>'#0075de','Blocked'=>'#ef4444','Done'=>'#1aae39'] as $s=>$c)
    <div class="legend-item"><div class="legend-dot" style="background:{{ $c }}"></div> {{ $s }}</div>
    @endforeach
</div>

<div class="gantt-wrap">
    <svg id="ganttChart"></svg>
</div>

{{-- Task count info --}}
<div style="font-size:13px;color:var(--ink-faint);margin-top:var(--sp-sm);text-align:right">
    Menampilkan {{ $tasks->count() }} task
</div>
@endif
@endsection

@push('scripts')
@if($tasks->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<script>
const statusColors = {
    'Todo': '#a39e98', 'In_Progress': '#0075de',
    'Blocked': '#ef4444', 'Done': '#1aae39'
};

@php
$ganttTasks = $tasks->map(fn($t) => [
    'id'           => 'T'.$t->id,
    'name'         => $t->nama,
    'start'        => $t->tanggal_mulai->format('Y-m-d'),
    'end'          => $t->tanggal_selesai->format('Y-m-d'),
    'progress'     => $t->persen_selesai,
    'custom_class' => 'status-'.$t->status,
    'dependencies' => $t->parent_task_id ? 'T'.$t->parent_task_id : '',
]);
$ganttPopup = $tasks->keyBy(fn($t) => 'T'.$t->id)->map(fn($t) => [
    'name'     => $t->nama,
    'progress' => $t->persen_selesai,
    'start'    => $t->tanggal_mulai->format('Y-m-d'),
    'end'      => $t->tanggal_selesai->format('Y-m-d'),
]);
@endphp

const tasks = @json($ganttTasks);
const popupData = @json($ganttPopup);

let gantt = new Gantt('#ganttChart', tasks, {
    view_mode: 'Day',
    date_format: 'YYYY-MM-DD',
    custom_popup_html: task => {
        const t = popupData[task.id];
        if (!t) return '';
        return `<div style="font-family:Inter,sans-serif;padding:8px 12px;min-width:180px">
            <strong style="font-size:13px">${t.name}</strong><br>
            <span style="font-size:12px;color:#615d59">Progress: ${t.progress}%</span><br>
            <span style="font-size:12px;color:#615d59">${t.start} → ${t.end}</span>
        </div>`;
    },
});

// Colour bars by status
document.querySelectorAll('.bar-wrapper').forEach(bar => {
    const cls = [...bar.classList].find(c => c.startsWith('status-'));
    if (cls) {
        const color = statusColors[cls.replace('status-', '')] || '#0075de';
        const barEl = bar.querySelector('.bar');
        if (barEl) barEl.style.fill = color;
    }
});

function setView(mode, btn) {
    gantt.change_view_mode(mode);
    document.querySelectorAll('.view-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    setTimeout(() => {
        document.querySelectorAll('.bar-wrapper').forEach(bar => {
            const cls = [...bar.classList].find(c => c.startsWith('status-'));
            if (cls) {
                const color = statusColors[cls.replace('status-', '')] || '#0075de';
                const barEl = bar.querySelector('.bar');
                if (barEl) barEl.style.fill = color;
            }
        });
    }, 200);
}
</script>
@else
<script>function setView() {}</script>
@endif
@endpush
