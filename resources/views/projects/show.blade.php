@extends('layouts.app')
@section('title', $project->nama)
@section('topbar-title', 'Overview Proyek')

@section('content')

{{-- Hero band --}}
<div style="background:var(--secondary);border-radius:var(--r-xl);padding:28px 32px;
     margin-bottom:28px;color:#fff;position:relative;overflow:hidden">
    <div style="position:absolute;top:-30px;right:-30px;width:220px;height:220px;
         border-radius:50%;background:rgba(255,255,255,.04)"></div>
    <div style="position:absolute;bottom:-50px;right:100px;width:140px;height:140px;
         border-radius:50%;background:rgba(255,255,255,.03)"></div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;
         flex-wrap:wrap;gap:16px;position:relative">
        <div>
            <span style="font-size:10px;font-weight:700;letter-spacing:1px;
                opacity:.5;text-transform:uppercase">{{ $project->kode }}</span>
            <h1 style="font-size:26px;font-weight:700;letter-spacing:-.5px;
                margin:6px 0 8px">{{ $project->nama }}</h1>
            @if($project->deskripsi)
            <p style="font-size:13px;opacity:.7;max-width:500px;line-height:1.5">
                {{ $project->deskripsi }}
            </p>
            @endif
            <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;align-items:center">
                @include('partials.project-status-badge', ['status'=>$project->status])
                <span style="font-size:12px;opacity:.6">
                    <i class="fas fa-calendar-days"></i>
                    {{ $project->tanggal_mulai?->format('d M Y') }} →
                    {{ $project->tanggal_selesai?->format('d M Y') }}
                </span>
                @php
                    $daysLeft = $project->tanggal_selesai ? (int) now()->diffInDays($project->tanggal_selesai, false) : null;
                @endphp
                @if($daysLeft !== null && $project->status === 'Active')
                <span style="font-size:11px;padding:2px 8px;border-radius:99px;
                    background:{{ $daysLeft < 0 ? 'rgba(239,68,68,.3)' : 'rgba(255,255,255,.12)' }};
                    color:{{ $daysLeft < 0 ? '#fca5a5' : 'rgba(255,255,255,.7)' }}">
                    {{ $daysLeft < 0 ? abs($daysLeft).' hari terlambat' : $daysLeft.' hari lagi' }}
                </span>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-pen"></i> Edit
            </a>
        </div>
    </div>
</div>

{{-- KPI Row --}}
@php
$taskTotal   = $project->tasks->count();
$taskDone    = $project->tasks->where('status','Done')->count();
$taskPct     = $taskTotal > 0 ? round($taskDone/$taskTotal*100) : 0;
$budgetReal  = $project->budgetBreakdowns->sum('realisasi');
$budgetPct   = $project->anggaran > 0 ? round($budgetReal/$project->anggaran*100,1) : 0;
$riskOpen    = $project->risks->whereNotIn('status',['Mitigated','Closed'])->count();
$msAchieved  = $project->milestones->where('status','Achieved')->count();
$msTotal     = $project->milestones->count();
@endphp
<div class="stats-grid section">
    <div class="stat-card stat-accent-blue">
        <div class="stat-label">Anggaran</div>
        <div class="stat-value" style="font-size:18px">Rp {{ number_format($project->anggaran,0,',','.') }}</div>
        <div style="margin-top:8px">
            <div class="progress-bar">
                <div class="progress-fill {{ $budgetPct>100?'danger':($budgetPct>80?'warning':'') }}"
                     style="width:{{ min($budgetPct,100) }}%"></div>
            </div>
            <span style="font-size:11px;color:var(--ink-faint)">{{ $budgetPct }}% terserap</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Progress Task</div>
        <div style="display:flex;align-items:baseline;gap:6px">
            <div class="stat-value" style="color:var(--primary)">{{ $taskPct }}%</div>
            <span style="font-size:12px;color:var(--ink-faint)">{{ $taskDone }}/{{ $taskTotal }}</span>
        </div>
        <div style="margin-top:8px">
            <div class="progress-bar">
                <div class="progress-fill {{ $taskPct>=100?'success':($taskPct>=50?'':'warning') }}"
                     style="width:{{ $taskPct }}%"></div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Milestone</div>
        <div class="stat-value">{{ $msAchieved }}<span style="font-size:14px;font-weight:400;color:var(--ink-faint)">/{{ $msTotal }}</span></div>
        <div class="stat-sub">{{ $project->milestones->where('status','Delayed')->count() > 0 ? $project->milestones->where('status','Delayed')->count().' delayed' : 'On track' }}</div>
    </div>
    <div class="stat-card {{ $riskOpen>0?'':'' }}" style="{{ $riskOpen>0?'border-color:#fecaca':'' }}">
        <div class="stat-label">Risiko Aktif</div>
        <div class="stat-value" style="color:{{ $riskOpen>0?'#b91c1c':'var(--ink)' }}">{{ $riskOpen }}</div>
        <div class="stat-sub">{{ $project->risks->count() }} total risiko tercatat</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Anggota Tim</div>
        <div class="stat-value">{{ $project->members->count() }}</div>
        <div class="stat-sub">{{ $project->members->pluck('peran')->unique()->count() }} peran berbeda</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Change Request</div>
        @php $crPending = $project->changes->where('status','Pending')->count(); @endphp
        <div class="stat-value" style="color:{{ $crPending>0?'#a16207':'var(--ink)' }}">{{ $project->changes->count() }}</div>
        <div class="stat-sub">{{ $crPending > 0 ? $crPending.' pending approval' : 'Tidak ada pending' }}</div>
    </div>
</div>

{{-- 2-col: Task status + Phase list --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px" class="section">

    {{-- Task breakdown --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Status Task</span>
            <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-utility btn-sm">Kelola →</a>
        </div>
        @php
        $statusItems = [
            ['label'=>'Done',        'val'=>$project->tasks->where('status','Done')->count(),        'color'=>'var(--accent-green)'],
            ['label'=>'In Progress', 'val'=>$project->tasks->where('status','In_Progress')->count(), 'color'=>'var(--primary)'],
            ['label'=>'Todo',        'val'=>$project->tasks->where('status','Todo')->count(),        'color'=>'var(--ink-faint)'],
            ['label'=>'Blocked',     'val'=>$project->tasks->where('status','Blocked')->count(),     'color'=>'#ef4444'],
        ];
        @endphp
        @foreach($statusItems as $si)
        @php $pct = $taskTotal > 0 ? round($si['val']/$taskTotal*100) : 0; @endphp
        <div style="margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
                <span style="color:var(--ink-secondary)">{{ $si['label'] }}</span>
                <span style="color:var(--ink-faint)">{{ $si['val'] }}</span>
            </div>
            <div class="progress-bar">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $si['color'] }};
                    border-radius:var(--r-full);transition:width .4s"></div>
            </div>
        </div>
        @endforeach
        <div style="display:flex;gap:6px;margin-top:10px;flex-wrap:wrap">
            @php $pm=['Low'=>'badge-grey','Medium'=>'badge-blue','High'=>'badge-orange','Critical'=>'badge-red']; @endphp
            @foreach(['Critical','High'] as $pr)
            @php $cnt = $project->tasks->where('prioritas',$pr)->count(); @endphp
            @if($cnt > 0)
            <span class="badge {{ $pm[$pr] }}">{{ $cnt }} {{ $pr }}</span>
            @endif
            @endforeach
        </div>
    </div>

    {{-- Phase list --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Fase Proyek</span>
            <a href="{{ route('projects.phases.index', $project) }}" class="btn btn-utility btn-sm">Kelola →</a>
        </div>
        @if($project->phases->isEmpty())
        <div style="text-align:center;padding:20px;color:var(--ink-faint);font-size:13px">
            Belum ada fase. <a href="{{ route('projects.phases.index',$project) }}" style="color:var(--primary)">Tambahkan →</a>
        </div>
        @else
        @foreach($project->phases->sortBy('urutan') as $ph)
        @php
        $phTasks = $project->tasks->where('phase_id', $ph->id);
        $phDone  = $phTasks->where('status','Done')->count();
        $phTotal = $phTasks->count();
        $phPct   = $phTotal > 0 ? round($phDone/$phTotal*100) : 0;
        $phMap   = ['Pending'=>'badge-grey','On_Progress'=>'badge-blue','Completed'=>'badge-green'];
        @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;
             border-bottom:1px solid var(--hairline)" class="last-no-border">
            <div style="width:22px;height:22px;border-radius:50%;background:var(--canvas-soft);
                 display:flex;align-items:center;justify-content:center;
                 font-size:10px;font-weight:700;color:var(--ink-muted);flex-shrink:0">
                {{ $ph->urutan }}
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:500;color:var(--ink)">{{ $ph->nama }}</div>
                <div style="margin-top:3px">
                    <div class="progress-bar" style="height:4px">
                        <div class="progress-fill {{ $phPct>=100?'success':'' }}"
                             style="width:{{ $phPct }}%"></div>
                    </div>
                </div>
            </div>
            <span class="badge {{ $phMap[$ph->status]??'badge-grey' }}">{{ $ph->status }}</span>
            <span style="font-size:11px;color:var(--ink-faint);white-space:nowrap">{{ $phPct }}%</span>
        </div>
        @endforeach
        @endif
    </div>
</div>

{{-- Recent milestones + risks --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px" class="section">

    {{-- Milestones --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Milestone Terbaru</span>
            <a href="{{ route('projects.milestones.index', $project) }}" class="btn btn-utility btn-sm">Semua →</a>
        </div>
        @if($project->milestones->isEmpty())
        <div style="text-align:center;padding:20px;color:var(--ink-faint);font-size:13px">
            Belum ada milestone.
        </div>
        @else
        @foreach($project->milestones->sortBy('tanggal_target')->take(5) as $ms)
        @php $msMap=['Pending'=>'badge-grey','Achieved'=>'badge-green','Delayed'=>'badge-red']; @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;
             border-bottom:1px solid var(--hairline)">
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:500;color:var(--ink)">{{ $ms->nama }}</div>
                <div style="font-size:11px;color:var(--ink-faint);margin-top:2px">
                    Target: {{ $ms->tanggal_target->format('d M Y') }}
                </div>
            </div>
            <span class="badge {{ $msMap[$ms->status]??'badge-grey' }}">{{ $ms->status }}</span>
        </div>
        @endforeach
        @endif
    </div>

    {{-- Active risks --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Risiko Aktif</span>
            <a href="{{ route('projects.risks.index', $project) }}" class="btn btn-utility btn-sm">Semua →</a>
        </div>
        @php $activeRisks = $project->risks->whereNotIn('status',['Mitigated','Closed'])->sortByDesc('skor_risiko')->take(5); @endphp
        @if($activeRisks->isEmpty())
        <div style="text-align:center;padding:20px;color:var(--ink-faint);font-size:13px">
            <i class="fas fa-shield-halved" style="font-size:24px;margin-bottom:8px;display:block"></i>
            Tidak ada risiko aktif.
        </div>
        @else
        @foreach($activeRisks as $r)
        @php
        $skor=$r->skor_risiko;
        $sb=$skor>=9?'badge-red':($skor>=6?'badge-orange':($skor>=3?'badge-yellow':'badge-green'));
        @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;
             border-bottom:1px solid var(--hairline)">
            <span class="badge {{ $sb }}" style="font-weight:700;flex-shrink:0">{{ $skor }}</span>
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;color:var(--ink);overflow:hidden;
                     text-overflow:ellipsis;white-space:nowrap">{{ $r->deskripsi_risiko }}</div>
                <div style="font-size:11px;color:var(--ink-faint)">{{ $r->probabilitas }} × {{ $r->dampak }}</div>
            </div>
            <span class="badge badge-grey" style="flex-shrink:0">{{ $r->status }}</span>
        </div>
        @endforeach
        @endif
    </div>
</div>

{{-- Quick nav to all sub-modules --}}
<div class="section">
    <div class="section-header">
        <span class="section-title">Kelola Proyek</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px">
        @php
        $navItems = [
            ['icon'=>'layer-group',       'label'=>'Fase & WBS',     'route'=>route('projects.phases.index',$project),     'color'=>'#62aef0','count'=>$project->phases->count()],
            ['icon'=>'list-check',        'label'=>'Task',           'route'=>route('projects.tasks.index',$project),      'color'=>'#d6b6f6','count'=>$taskTotal],
            ['icon'=>'bars-progress',     'label'=>'Gantt Chart',    'route'=>route('projects.gantt.index',$project),      'color'=>'#0075de','count'=>null],
            ['icon'=>'flag-checkered',    'label'=>'Milestone',      'route'=>route('projects.milestones.index',$project), 'color'=>'#f59e0b','count'=>$msTotal],
            ['icon'=>'wallet',            'label'=>'Anggaran',       'route'=>route('projects.budget.index',$project),     'color'=>'#1aae39','count'=>$project->budgetBreakdowns->count()],
            ['icon'=>'boxes-stacked',     'label'=>'Resource',       'route'=>route('projects.resources.index',$project),  'color'=>'#2a9d99','count'=>null],
            ['icon'=>'handshake',         'label'=>'Vendor',         'route'=>route('projects.outsourcing.index',$project),'color'=>'#dd5b00','count'=>null],
            ['icon'=>'users',             'label'=>'Anggota Tim',    'route'=>route('projects.members.index',$project),    'color'=>'#7e22ce','count'=>$project->members->count()],
            ['icon'=>'chart-line',        'label'=>'Kurva-S',        'route'=>route('projects.kurvas.index',$project),     'color'=>'#2a9d99','count'=>null],
            ['icon'=>'triangle-exclamation','label'=>'Risiko',       'route'=>route('projects.risks.index',$project),      'color'=>'#dd5b00','count'=>$project->risks->count()],
            ['icon'=>'code-branch',       'label'=>'Change Request', 'route'=>route('projects.changes.index',$project),   'color'=>'#ff64c8','count'=>$project->changes->count()],
        ];
        @endphp
        @foreach($navItems as $item)
        <a href="{{ $item['route'] }}" style="text-decoration:none">
            <div class="card" style="cursor:pointer;transition:box-shadow .15s,border-color .15s;padding:16px"
                 onmouseover="this.style.boxShadow='var(--shadow-1)';this.style.borderColor='{{ $item['color'] }}40'"
                 onmouseout="this.style.boxShadow='none';this.style.borderColor='var(--hairline)'">
                <div style="font-size:20px;margin-bottom:8px;color:{{ $item['color'] }}">
                    <i class="fas fa-{{ $item['icon'] }}"></i>
                </div>
                <div style="font-size:13px;font-weight:600;color:var(--ink)">{{ $item['label'] }}</div>
                @if($item['count'] !== null)
                <div style="font-size:11px;color:var(--ink-faint);margin-top:2px">{{ $item['count'] }} item</div>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</div>

<style>
.last-no-border:last-child { border-bottom: none !important; }
</style>
@endsection
