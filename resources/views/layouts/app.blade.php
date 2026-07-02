<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Manajemen Proyek</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('styles')
</head>
<body>

@php
    // Step 1: cek route binding saat ini (paling akurat)
    $routeParam = request()->route('project');
    $currentProject = ($routeParam instanceof \App\Models\Project) ? $routeParam : null;

    // Step 2: simpan ke session kalau ada project di URL
    if ($currentProject) {
        session(['sidebar_project_id' => $currentProject->id]);
    }

    // Step 3: fallback ke session kalau tidak ada project di URL
    if (!$currentProject && session('sidebar_project_id')) {
        $currentProject = \App\Models\Project::find(session('sidebar_project_id'));
    }

    // Step 4: load relasi untuk badge sidebar
    if ($currentProject instanceof \App\Models\Project) {
        $needs = ['tasks', 'risks', 'changes', 'members', 'phases'];
        $toLoad = array_values(array_filter($needs, fn($r) => !$currentProject->relationLoaded($r)));
        if ($toLoad) $currentProject->load($toLoad);
    }

    $p = $currentProject;
    $allProjects = \App\Models\Project::orderBy('nama')->get(['id','nama','kode','status']);

    // Cek role user di project aktif untuk conditional UI
    $isProjectManager = false;
    $userProjectRole  = null;
    if ($currentProject && auth()->check()) {
        $membership = \App\Models\ProjectMember::where('project_id', $currentProject->id)
            ->where('user_id', auth()->id())
            ->first();
        $userProjectRole  = $membership?->peran ?? ($currentProject->created_by === auth()->id() ? 'Owner' : null);
        $isProjectManager = in_array($userProjectRole, ['Owner', 'Project_Manager']);
    }
@endphp

<nav class="sidebar" id="sidebar">
    <div class="sb-brand">
        <div class="sb-brand-eyebrow">Internal Tools</div>
        <div class="sb-brand-title">📊 Manajemen Proyek</div>
    </div>

    {{-- Project Switcher Dropdown --}}
    <div style="padding:8px 10px 0">
        <div style="font-size:10px;font-weight:700;letter-spacing:.8px;
             color:rgba(255,255,255,.3);text-transform:uppercase;margin-bottom:5px">
            Proyek Aktif
        </div>
        <div style="position:relative">
            <select id="projectSwitcher" onchange="switchProject(this.value)"
                style="width:100%;background:rgba(255,255,255,.09);border:1px solid rgba(255,255,255,.14);
                    color:#fff;border-radius:6px;padding:7px 28px 7px 10px;font-size:12px;
                    font-family:inherit;cursor:pointer;appearance:none;outline:none;line-height:1.3">
                <option value="" style="background:#1a1a1a;color:#aaa">— Pilih Proyek —</option>
                @foreach($allProjects as $proj)
                <option value="{{ $proj->id }}" style="background:#1a1a1a;color:#fff"
                    {{ $p && $p->id === $proj->id ? 'selected' : '' }}>
                    {{ $proj->kode }} · {{ Str::limit($proj->nama, 20) }}
                </option>
                @endforeach
            </select>
            <span style="position:absolute;right:9px;top:50%;transform:translateY(-50%);
                pointer-events:none;color:rgba(255,255,255,.35);font-size:9px">▼</span>
        </div>
    </div>

    <div class="sb-divider" style="margin:10px 0 2px"></div>

    <div class="sb-section" style="padding-top:2px">
        <a href="{{ route('dashboard') }}"
           class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-chart-pie"></i></span> Executive Dashboard
        </a>

        <a href="{{ $p ? route('projects.show', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.show') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-gauge-high"></i></span> Project Dashboard
        </a>

        <a href="{{ route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.index') || request()->routeIs('projects.create') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-folder-open"></i></span> Project Management
        </a>

        <a href="{{ route('projects.create') }}"
           class="sb-link {{ request()->routeIs('projects.create') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-plus"></i></span> Tambah Proyek Baru
        </a>

        <a href="{{ $p ? route('projects.members.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.members.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-users"></i></span> Resource Management
        </a>

        <a href="{{ $p ? route('projects.gantt.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.gantt.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-bars-progress"></i></span> Gantt Chart Analysis
        </a>

        <a href="{{ $p ? route('projects.tasks.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.tasks.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-list-check"></i></span> Task Management
            @if($p && $p->tasks->count() > 0)
            <span class="sb-badge">{{ $p->tasks->count() }}</span>
            @endif
        </a>

        <a href="{{ $p ? route('projects.risks.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.risks.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-triangle-exclamation"></i></span> Risk &amp; Issue Management
            @if($p)
            @php $riskOpen = $p->risks->whereNotIn('status',['Mitigated','Closed'])->count() @endphp
            @if($riskOpen > 0)<span class="sb-badge" style="background:#ef4444;color:#fff">{{ $riskOpen }}</span>@endif
            @endif
        </a>

        <a href="{{ $p ? route('projects.budget.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.budget.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-wallet"></i></span> Budget Management
        </a>

        <a href="{{ $p ? route('projects.changes.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.changes.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-code-branch"></i></span> Change Request
            @if($p)
            @php $crPending = $p->changes->where('status','Pending')->count() @endphp
            @if($crPending > 0)<span class="sb-badge" style="background:#f59e0b;color:#000">{{ $crPending }}</span>@endif
            @endif
        </a>

        <a href="{{ $p ? route('projects.milestones.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.milestones.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-flag-checkered"></i></span> Milestone Tracker
        </a>

        <a href="{{ $p ? route('projects.phases.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.phases.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-layer-group"></i></span> Phase Management
        </a>

        <a href="{{ $p ? route('projects.outsourcing.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.outsourcing.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-handshake"></i></span> Vendor / Outsourcing
        </a>

        <a href="{{ $p ? route('projects.resources.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.resources.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-boxes-stacked"></i></span> Resource Allocation
        </a>

        <a href="{{ $p ? route('projects.kurvas.index', $p) : route('projects.index') }}"
           class="sb-link {{ request()->routeIs('projects.kurvas.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-chart-line"></i></span> Kurva-S / EVM
        </a>
    </div>

    <div class="sb-user">
        <div class="sb-user-row">
            <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}</div>
            <div style="min-width:0;flex:1">
                <div class="sb-user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="sb-user-email">{{ auth()->user()->email ?? '' }}</div>
                @if($userProjectRole)
                <div style="margin-top:3px">
                    <span style="font-size:9px;font-weight:700;padding:1px 6px;border-radius:99px;
                        background:{{ $isProjectManager ? 'var(--primary)' : 'rgba(255,255,255,.12)' }};
                        color:{{ $isProjectManager ? '#fff' : 'rgba(255,255,255,.5)' }}">
                        {{ str_replace('_',' ',$userProjectRole) }}
                    </span>
                </div>
                @endif
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-link" style="width:100%;border:none;cursor:pointer;
                margin-top:2px;color:rgba(255,255,255,.45)">
                <span class="sb-icon"><i class="fas fa-right-from-bracket"></i></span> Keluar
            </button>
        </form>
    </div>
</nav>


<div class="main-wrap">
    <header class="topbar">
        <div class="topbar-left">
            {{-- Breadcrumb --}}
            <div class="topbar-breadcrumb">
                @if($currentProject)
                    <a href="{{ route('projects.index') }}">Proyek</a>
                    <span class="sep">/</span>
                    <a href="{{ route('projects.show', $currentProject) }}">{{ Str::limit($currentProject->nama, 24) }}</a>
                    @hasSection('topbar-sub')
                        <span class="sep">/</span>
                        <span class="topbar-title">@yield('topbar-sub')</span>
                    @else
                        <span class="sep">/</span>
                        <span class="topbar-title">@yield('topbar-title', 'Detail')</span>
                    @endif
                @else
                    <span class="topbar-title">@yield('topbar-title', 'Dashboard')</span>
                @endif
            </div>
        </div>
        <div class="topbar-right">
            @if(session('success'))
                <span style="font-size:12px;color:var(--accent-green)">
                    <i class="fas fa-circle-check"></i> {{ session('success') }}
                </span>
            @endif
            <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}</div>
        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
function openModal(id){document.getElementById(id).classList.add('open')}
function closeModal(id){document.getElementById(id).classList.remove('open')}
function switchProject(projectId) {
    if (!projectId) return;
    // Redirect to project show page — this sets session via AppServiceProvider
    // Then all sidebar links will work correctly
    window.location.href = '/projects/' + projectId;
}
function showToast(msg,type='success'){
    const c=document.getElementById('toastContainer'),t=document.createElement('div');
    t.className='toast '+type;
    t.innerHTML=`<i class="fas fa-${type==='success'?'circle-check':'circle-xmark'}"></i> ${msg}`;
    c.appendChild(t);setTimeout(()=>t.remove(),3500)
}
document.addEventListener('DOMContentLoaded',()=>{
    document.querySelectorAll('.modal-backdrop').forEach(m=>{
        m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('open')})
    })

    // ── Currency input: format display, strip sebelum submit ──
    const moneySelectors = [
        'input[name="anggaran"]',
        'input[name="realisasi"]',
        'input[name="nilai_kontrak"]',
        'input[name="biaya_perubahan"]',
        'input[name="biaya_satuan"]',
        'input[data-currency]',
    ].join(', ');

    function toDisplay(raw) {
        const n = String(raw).replace(/\D/g, '');
        if (!n) return '';
        return Number(n).toLocaleString('id-ID'); // format: 1.500.000
    }
    function toRaw(display) {
        // Strip titik (pemisah ribuan id-ID) dan karakter non-digit
        return display.replace(/\./g, '').replace(/[^\d]/g, '');
    }

    // Format semua money inputs yang sudah ada nilainya
    document.querySelectorAll(moneySelectors).forEach(el => {
        if (el.type === 'hidden' || el.readOnly || el.disabled) return;
        // Ubah type ke text untuk support formatting
        el.setAttribute('inputmode', 'numeric');
        // Format nilai awal
        if (el.value) el.value = toDisplay(el.value);

        el.addEventListener('input', function() {
            const raw   = toRaw(this.value);
            const pos   = this.selectionStart;
            const oldLen = this.value.length;
            this.value  = toDisplay(raw);
            const diff  = this.value.length - oldLen;
            this.setSelectionRange(pos + diff, pos + diff);
        });

        el.addEventListener('blur', function() {
            this.value = toDisplay(toRaw(this.value));
        });
    });

    // Strip titik sebelum setiap form submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            this.querySelectorAll(moneySelectors).forEach(el => {
                if (el.type !== 'hidden') {
                    el.value = toRaw(el.value);
                }
            });
        });
    });
})
</script>
@stack('scripts')
</body>
</html>
