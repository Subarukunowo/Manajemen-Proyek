@extends('layouts.app')
@section('title', 'Executive Dashboard')
@section('topbar-title', 'Executive Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Executive Dashboard</h1>
    <p class="page-desc">Ringkasan eksekutif seluruh portofolio proyek.</p>
</div>

{{-- ── Row 1: Project stats ────────────────────────────────── --}}
<div class="stats-grid section">
    <div class="stat-card stat-accent-blue">
        <div class="stat-label">Total Proyek</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-sub">{{ $stats['active'] }} aktif · {{ $stats['draft'] }} draft</div>
    </div>
    <div class="stat-card stat-accent-green">
        <div class="stat-label">Proyek Selesai</div>
        <div class="stat-value">{{ $stats['completed'] }}</div>
        <div class="stat-sub">{{ $stats['total'] > 0 ? round($stats['completed']/$stats['total']*100) : 0 }}% dari total</div>
    </div>
    <div class="stat-card stat-accent-teal">
        <div class="stat-label">Total Anggaran</div>
        <div class="stat-value" style="font-size:20px">Rp {{ number_format($stats['total_budget'],0,',','.') }}</div>
        <div style="margin-top:8px">
            <div class="progress-bar"><div class="progress-fill {{ $budgetPct>90?'danger':($budgetPct>70?'warning':'') }}" style="width:{{ min($budgetPct,100) }}%"></div></div>
            <span style="font-size:11px;color:var(--ink-faint)">{{ $budgetPct }}% terserap</span>
        </div>
    </div>
    <div class="stat-card {{ $riskStats['critical']>0 ? '' : '' }}">
        <div class="stat-label">Risiko Kritis</div>
        <div class="stat-value" style="color:{{ $riskStats['critical']>0?'#b91c1c':'var(--ink)' }}">{{ $riskStats['critical'] }}</div>
        <div class="stat-sub">{{ $riskStats['open'] }} risiko terbuka dari {{ $riskStats['total'] }} total</div>
    </div>
</div>

{{-- ── Row 2: Task + Milestone stats ─────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-md)" class="section">

    {{-- Task distribution --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Status Task</span>
            <span style="font-size:13px;color:var(--ink-faint)">Total {{ $taskStats['total'] }}</span>
        </div>
        @php
        $taskItems = [
            ['label'=>'Done',        'val'=>$taskStats['done'],        'color'=>'var(--accent-green)'],
            ['label'=>'In Progress', 'val'=>$taskStats['in_progress'], 'color'=>'var(--primary)'],
            ['label'=>'Todo',        'val'=>$taskStats['todo'],        'color'=>'var(--ink-faint)'],
            ['label'=>'Blocked',     'val'=>$taskStats['blocked'],     'color'=>'#ef4444'],
        ];
        @endphp
        @foreach($taskItems as $ti)
        @php $pct = $taskStats['total']>0 ? round($ti['val']/$taskStats['total']*100) : 0; @endphp
        <div style="margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
                <span style="color:var(--ink-secondary)">{{ $ti['label'] }}</span>
                <span style="color:var(--ink-faint)">{{ $ti['val'] }} ({{ $pct }}%)</span>
            </div>
            <div class="progress-bar">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $ti['color'] }};border-radius:var(--r-full);transition:width .4s"></div>
            </div>
        </div>
        @endforeach
        <div style="display:flex;gap:var(--sp-xs);margin-top:var(--sp-sm);flex-wrap:wrap">
            <span class="badge badge-red">{{ $taskStats['critical'] }} Critical</span>
            <span class="badge badge-orange">{{ $taskStats['high'] }} High</span>
        </div>
    </div>

    {{-- Milestone status --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Status Milestone</span>
            <span style="font-size:13px;color:var(--ink-faint)">Total {{ $milestoneStats['total'] }}</span>
        </div>
        @php
        $msItems = [
            ['label'=>'Achieved', 'val'=>$milestoneStats['achieved'], 'color'=>'var(--accent-green)'],
            ['label'=>'Pending',  'val'=>$milestoneStats['pending'],  'color'=>'var(--accent-sky)'],
            ['label'=>'Delayed',  'val'=>$milestoneStats['delayed'],  'color'=>'#ef4444'],
        ];
        @endphp
        @foreach($msItems as $mi)
        @php $pct = $milestoneStats['total']>0 ? round($mi['val']/$milestoneStats['total']*100) : 0; @endphp
        <div style="margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
                <span style="color:var(--ink-secondary)">{{ $mi['label'] }}</span>
                <span style="color:var(--ink-faint)">{{ $mi['val'] }} ({{ $pct }}%)</span>
            </div>
            <div class="progress-bar">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $mi['color'] }};border-radius:var(--r-full);transition:width .4s"></div>
            </div>
        </div>
        @endforeach
        @if($milestoneStats['delayed']>0)
        <div style="margin-top:var(--sp-sm);padding:8px 10px;background:#fee2e2;border-radius:var(--r-md);font-size:13px;color:#b91c1c">
            <i class="fas fa-exclamation-circle"></i> {{ $milestoneStats['delayed'] }} milestone terlambat
        </div>
        @endif
    </div>
</div>

{{-- ── Row 3: Charts row ──────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-md)" class="section">
    <div class="card">
        <div class="card-header"><span class="card-title">Distribusi Status Proyek</span></div>
        <div style="position:relative;height:200px">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">Penyerapan Anggaran per Proyek</span></div>
        <div style="position:relative;height:200px">
            <canvas id="budgetChart"></canvas>
        </div>
    </div>
</div>

{{-- ── Row 4: Projects table — pure read only ─── --}}
<div class="section">
    <div class="section-header">
        <span class="section-title">Semua Proyek</span>
        <a href="{{ route('projects.index') }}" class="btn btn-utility btn-sm">Kelola Proyek →</a>
    </div>
    <div class="card">
        @if($projects->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
            <p>Belum ada proyek. Mulai dari menu <strong>Semua Proyek</strong> di sidebar.</p>
        </div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Kode</th><th>Nama</th><th>Status</th><th>Anggaran</th><th>Progress Task</th><th>Risiko</th><th>Milestone</th><th></th></tr></thead>
                <tbody>
                @foreach($projects->sortByDesc('created_at') as $p)
                @php
                    $done  = $p->tasks->where('status','Done')->count();
                    $total = $p->tasks->count();
                    $pct   = $total>0 ? round($done/$total*100) : 0;
                @endphp
                <tr>
                    <td><span style="font-family:monospace;font-size:11px;color:var(--ink-muted)">{{ $p->kode }}</span></td>
                    <td><a href="{{ route('projects.show',$p) }}" style="font-weight:600;color:var(--ink);text-decoration:none">{{ $p->nama }}</a></td>
                    <td>@include('partials.project-status-badge',['status'=>$p->status])</td>
                    <td style="font-size:12px">Rp {{ number_format($p->anggaran,0,',','.') }}</td>
                    <td style="min-width:120px">
                        <div style="display:flex;align-items:center;gap:6px">
                            <div class="progress-bar" style="flex:1"><div class="progress-fill {{ $pct>=100?'success':($pct>=50?'':'warning') }}" style="width:{{ $pct }}%"></div></div>
                            <span style="font-size:11px;color:var(--ink-faint)">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td style="font-size:12px;color:{{ $p->risks->whereNotIn('status',['Mitigated','Closed'])->count()>0?'#b91c1c':'var(--ink-muted)' }}">
                        {{ $p->risks->whereNotIn('status',['Mitigated','Closed'])->count() }}
                    </td>
                    <td style="font-size:12px;color:var(--ink-muted)">
                        {{ $p->milestones->where('status','Achieved')->count() }}/{{ $p->milestones->count() }}
                    </td>
                    <td><a href="{{ route('projects.show',$p) }}" class="btn btn-icon" title="Detail"><i class="fas fa-arrow-right"></i></a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const font = { family: 'Inter', size: 12 };
const hairline = '#e6e6e6';

// Status doughnut
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: @json($statusDist['labels']),
        datasets: [{
            data: @json($statusDist['data']),
            backgroundColor: ['#0075de','#1aae39','#a39e98','#f59e0b','#ef4444'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right', labels: { font, color: '#615d59', boxWidth: 12 } }
        },
        cutout: '65%',
    }
});

// Budget bar
const budgetLabels = @json($projects->pluck('nama')->map(fn($n)=>Str::limit($n,18))->values());
const budgetData   = @json($projects->map(fn($p)=>round($p->budgetBreakdowns->sum('realisasi')/$p->anggaran*100,1))->values());
new Chart(document.getElementById('budgetChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($projects->map(fn($p) => \Str::limit($p->nama, 16))->values()) !!},
        datasets: [{
            label: 'Penyerapan (%)',
            data: {!! json_encode($projects->map(fn($p) => $p->anggaran > 0 ? round($p->budgetBreakdowns->sum('realisasi')/$p->anggaran*100,1) : 0)->values()) !!},
            backgroundColor: '#0075de',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { max: 100, grid: { color: hairline }, ticks: { font, color: '#a39e98', callback: v=>v+'%' } },
            y: { grid: { display: false }, ticks: { font, color: '#615d59' } }
        }
    }
});
</script>
@endpush
