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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary:#0075de; --primary-active:#005bab; --secondary:#213183;
            --on-primary:#fff; --canvas:#fff; --canvas-soft:#f6f5f4;
            --surface:#fff; --ink:#000; --ink-secondary:#31302e;
            --ink-muted:#615d59; --ink-faint:#a39e98; --hairline:#e6e6e6;
            --accent-sky:#62aef0; --accent-purple:#d6b6f6; --accent-pink:#ff64c8;
            --accent-orange:#dd5b00; --accent-teal:#2a9d99; --accent-green:#1aae39;
            --r-xs:4px; --r-sm:5px; --r-md:8px; --r-lg:12px; --r-xl:16px; --r-full:9999px;
            --sp-xxs:4px; --sp-xs:8px; --sp-sm:12px; --sp-md:16px;
            --sp-lg:24px; --sp-xl:28px; --sp-xxl:32px;
            --shadow-1:0 0.175px 1.041px rgba(0,0,0,.01),0 0.8px 2.925px rgba(0,0,0,.02),0 2.025px 7.847px rgba(0,0,0,.027),0 4px 18px rgba(0,0,0,.04);
            --shadow-2:0 2px 8px rgba(0,0,0,.04),0 8px 24px rgba(0,0,0,.06),0 23px 52px rgba(0,0,0,.05);
            --sidebar-w:260px;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%}
        body{font-family:'Inter',-apple-system,system-ui,'Segoe UI',sans-serif;
             font-size:15px;line-height:1.4;background:var(--canvas-soft);
             color:var(--ink);display:flex;min-height:100vh}
        /* ─── Sidebar ─── */
        .sidebar{width:var(--sidebar-w);min-height:100vh;background:#111110;
            display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;
            z-index:100;overflow-y:auto}
        .sb-brand{padding:20px 16px 16px;border-bottom:1px solid rgba(255,255,255,.07)}
        .sb-brand-eyebrow{font-size:10px;font-weight:700;letter-spacing:1px;
            color:rgba(255,255,255,.35);text-transform:uppercase;margin-bottom:4px}
        .sb-brand-title{font-size:15px;font-weight:700;color:#fff;letter-spacing:-.25px}
        /* nav sections */
        .sb-section{padding:12px 8px 4px}
        .sb-section-label{font-size:10px;font-weight:700;letter-spacing:.9px;
            color:rgba(255,255,255,.3);text-transform:uppercase;
            padding:0 8px 6px;display:block}
        .sb-link{display:flex;align-items:center;gap:9px;padding:7px 10px;
            border-radius:6px;color:rgba(255,255,255,.6);font-size:13px;font-weight:500;
            text-decoration:none;transition:background .1s,color .1s;cursor:pointer;
            white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .sb-link:hover{background:rgba(255,255,255,.07);color:#fff}
        .sb-link.active{background:var(--primary);color:#fff}
        .sb-link .sb-icon{width:18px;text-align:center;font-size:12px;flex-shrink:0}
        .sb-link .sb-badge{margin-left:auto;font-size:10px;font-weight:700;
            background:rgba(255,255,255,.15);color:rgba(255,255,255,.7);
            padding:1px 6px;border-radius:99px;flex-shrink:0}
        .sb-link.active .sb-badge{background:rgba(255,255,255,.25);color:#fff}
        /* divider inside sidebar */
        .sb-divider{height:1px;background:rgba(255,255,255,.07);margin:8px 12px}
        /* project context header in sidebar */
        .sb-project-header{padding:10px 16px 6px;
            background:rgba(255,255,255,.04);border-radius:8px;margin:0 8px 4px}
        .sb-project-kode{font-size:10px;font-weight:600;letter-spacing:.5px;
            color:rgba(255,255,255,.35);text-transform:uppercase;margin-bottom:2px}
        .sb-project-name{font-size:13px;font-weight:600;color:rgba(255,255,255,.85);
            overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        /* user footer */
        .sb-user{padding:12px 8px;border-top:1px solid rgba(255,255,255,.07);margin-top:auto}
        .sb-user-row{display:flex;align-items:center;gap:9px;
            padding:6px 10px;border-radius:6px}
        .sb-avatar{width:26px;height:26px;border-radius:50%;background:var(--primary);
            display:flex;align-items:center;justify-content:center;
            font-size:11px;font-weight:700;color:#fff;flex-shrink:0}
        .sb-user-name{font-size:12px;font-weight:600;color:rgba(255,255,255,.8);
            overflow:hidden;text-overflow:ellipsis;white-space:nowrap;line-height:1.2}
        .sb-user-email{font-size:10px;color:rgba(255,255,255,.3);
            overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        /* ─── Main ─── */
        .main-wrap{margin-left:var(--sidebar-w);flex:1;
            display:flex;flex-direction:column;min-height:100vh}
        .topbar{background:var(--canvas);border-bottom:1px solid var(--hairline);
            padding:0 28px;height:54px;display:flex;align-items:center;
            justify-content:space-between;position:sticky;top:0;z-index:50}
        .topbar-left{display:flex;align-items:center;gap:8px}
        .topbar-breadcrumb{font-size:13px;color:var(--ink-muted)}
        .topbar-breadcrumb a{color:var(--ink-muted);text-decoration:none}
        .topbar-breadcrumb a:hover{color:var(--ink)}
        .topbar-breadcrumb .sep{margin:0 6px;color:var(--ink-faint)}
        .topbar-title{font-size:14px;font-weight:600;color:var(--ink)}
        .topbar-right{display:flex;align-items:center;gap:12px}
        .topbar-avatar{width:30px;height:30px;border-radius:50%;background:var(--primary);
            color:#fff;display:flex;align-items:center;justify-content:center;
            font-size:12px;font-weight:700}
        .page-content{padding:32px;flex:1}
        /* ─── Buttons ─── */
        .btn{display:inline-flex;align-items:center;gap:7px;font-family:inherit;
            font-size:14px;font-weight:500;line-height:1.5;border:none;cursor:pointer;
            text-decoration:none;transition:background .12s,transform .08s}
        .btn:active{transform:scale(.97)}
        .btn-primary{background:var(--primary);color:#fff;padding:7px 18px;border-radius:var(--r-full)}
        .btn-primary:hover{background:var(--primary-active);color:#fff}
        .btn-secondary{background:var(--surface);color:var(--ink);padding:7px 18px;
            border-radius:var(--r-full);box-shadow:var(--shadow-1);border:1px solid var(--hairline)}
        .btn-secondary:hover{background:var(--canvas-soft)}
        .btn-utility{background:var(--surface);color:var(--ink);padding:5px 13px;
            border-radius:var(--r-md);border:1px solid var(--hairline);font-size:13px}
        .btn-utility:hover{background:var(--canvas-soft)}
        .btn-sm{font-size:12px;padding:4px 12px}
        .btn-icon{padding:5px 7px;border-radius:var(--r-md);background:transparent;
            color:var(--ink-muted);border:1px solid transparent;cursor:pointer;font-size:13px}
        .btn-icon:hover{background:var(--canvas-soft);border-color:var(--hairline);color:var(--ink)}
        /* ─── Cards ─── */
        .card{background:var(--surface);border-radius:var(--r-lg);
            border:1px solid var(--hairline);padding:var(--sp-lg)}
        .card-elevated{box-shadow:var(--shadow-1)}
        .card-header{display:flex;align-items:center;justify-content:space-between;
            margin-bottom:var(--sp-md)}
        .card-title{font-size:14px;font-weight:600;color:var(--ink)}
        /* ─── Stat cards ─── */
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));
            gap:var(--sp-md)}
        .stat-card{background:var(--surface);border:1px solid var(--hairline);
            border-radius:var(--r-lg);padding:var(--sp-lg)}
        .stat-label{font-size:11px;font-weight:600;color:var(--ink-muted);
            text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
        .stat-value{font-size:24px;font-weight:700;letter-spacing:-.5px;color:var(--ink)}
        .stat-sub{font-size:12px;color:var(--ink-faint);margin-top:4px}
        .stat-accent-blue .stat-value{color:var(--primary)}
        .stat-accent-green .stat-value{color:var(--accent-green)}
        .stat-accent-orange .stat-value{color:var(--accent-orange)}
        .stat-accent-teal .stat-value{color:var(--accent-teal)}
        /* ─── Page header ─── */
        .page-header{margin-bottom:28px}
        .page-title{font-size:24px;font-weight:700;letter-spacing:-.5px;color:var(--ink)}
        .page-desc{font-size:14px;color:var(--ink-muted);margin-top:5px}
        /* ─── Badges ─── */
        .badge{display:inline-flex;align-items:center;font-size:11px;font-weight:600;
            letter-spacing:.1px;padding:2px 8px;border-radius:var(--r-full)}
        .badge-blue{background:#dbeafe;color:#1d4ed8}
        .badge-green{background:#dcfce7;color:#15803d}
        .badge-yellow{background:#fef9c3;color:#a16207}
        .badge-red{background:#fee2e2;color:#b91c1c}
        .badge-purple{background:#f3e8ff;color:#7e22ce}
        .badge-orange{background:#ffedd5;color:#c2410c}
        .badge-teal{background:#ccfbf1;color:#0f766e}
        .badge-grey{background:var(--canvas-soft);color:var(--ink-muted)}
        /* ─── Table ─── */
        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse;font-size:13px}
        thead th{background:var(--canvas-soft);color:var(--ink-muted);font-size:10px;
            font-weight:700;letter-spacing:.6px;text-transform:uppercase;
            padding:10px 14px;text-align:left;border-bottom:1px solid var(--hairline)}
        tbody td{padding:10px 14px;border-bottom:1px solid var(--hairline);
            color:var(--ink-secondary);vertical-align:middle}
        tbody tr:last-child td{border-bottom:none}
        tbody tr:hover td{background:#f9f9f8}
        /* ─── Form ─── */
        .form-group{margin-bottom:var(--sp-md)}
        .form-label{display:block;font-size:12px;font-weight:600;
            color:var(--ink-secondary);margin-bottom:5px;text-transform:uppercase;letter-spacing:.3px}
        .form-input,.form-select,.form-textarea{width:100%;padding:8px 10px;
            font-family:inherit;font-size:14px;color:var(--ink);background:var(--surface);
            border:1px solid #ddd;border-radius:var(--r-xs);outline:none;
            transition:border-color .12s,box-shadow .12s}
        .form-input:focus,.form-select:focus,.form-textarea:focus{
            border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,117,222,.1)}
        .form-textarea{resize:vertical;min-height:80px}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-md)}
        .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--sp-md)}
        .field-error{font-size:11px;color:#ef4444;margin-top:3px;display:block}
        /* ─── Modal ─── */
        .modal-backdrop{display:none;position:fixed;inset:0;
            background:rgba(0,0,0,.4);z-index:200;
            align-items:center;justify-content:center}
        .modal-backdrop.open{display:flex}
        .modal{background:var(--surface);border-radius:var(--r-xl);
            box-shadow:var(--shadow-2);padding:var(--sp-xxl);
            width:100%;max-width:560px;max-height:90vh;overflow-y:auto}
        .modal-lg{max-width:720px}
        .modal-header{display:flex;align-items:flex-start;justify-content:space-between;
            margin-bottom:var(--sp-lg)}
        .modal-title{font-size:18px;font-weight:700;letter-spacing:-.25px}
        .modal-close{background:none;border:none;cursor:pointer;font-size:20px;
            color:var(--ink-faint);line-height:1}
        .modal-close:hover{color:var(--ink)}
        .modal-footer{display:flex;justify-content:flex-end;gap:var(--sp-sm);
            margin-top:var(--sp-lg);padding-top:var(--sp-md);border-top:1px solid var(--hairline)}
        /* ─── Progress ─── */
        .progress-bar{height:6px;background:var(--hairline);border-radius:var(--r-full);overflow:hidden}
        .progress-fill{height:100%;background:var(--primary);border-radius:var(--r-full);transition:width .4s}
        .progress-fill.danger{background:#ef4444}
        .progress-fill.warning{background:#f59e0b}
        .progress-fill.success{background:var(--accent-green)}
        /* ─── Empty state ─── */
        .empty-state{background:var(--canvas-soft);border-radius:var(--r-xl);
            padding:var(--sp-xxl);text-align:center}
        .empty-state .empty-icon{font-size:32px;color:var(--ink-faint);margin-bottom:var(--sp-md)}
        .empty-state p{color:var(--ink-muted);font-size:14px}
        /* ─── Section ─── */
        .section{margin-bottom:var(--sp-xxl)}
        .section-header{display:flex;align-items:center;justify-content:space-between;
            margin-bottom:var(--sp-md)}
        .section-title{font-size:15px;font-weight:600;letter-spacing:-.125px}
        /* ─── Alert ─── */
        .alert{padding:10px 14px;border-radius:var(--r-md);margin-bottom:var(--sp-md);
            font-size:13px;display:flex;align-items:center;gap:8px}
        .alert-success{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
        .alert-error{background:#fee2e2;color:#b91c1c;border:1px solid #fecaca}
        /* ─── Toast ─── */
        .toast-container{position:fixed;bottom:20px;right:20px;z-index:999;
            display:flex;flex-direction:column;gap:8px}
        .toast{background:var(--surface);border-radius:var(--r-xl);box-shadow:var(--shadow-2);
            padding:10px 16px;font-size:13px;display:flex;align-items:center;gap:8px;
            min-width:260px;border-left:3px solid var(--primary);animation:slideIn .2s ease}
        .toast.success{border-color:var(--accent-green)}
        .toast.error{border-color:#ef4444}
        @keyframes slideIn{from{transform:translateX(20px);opacity:0}to{transform:translateX(0);opacity:1}}
        /* ─── Responsive ─── */
        @media(max-width:768px){
            .sidebar{transform:translateX(-100%);transition:transform .2s}
            .sidebar.open{transform:translateX(0)}
            .main-wrap{margin-left:0}
            .form-row,.form-row-3{grid-template-columns:1fr}
            .stats-grid{grid-template-columns:1fr 1fr}
            .page-content{padding:16px}
        }
    </style>
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
})
</script>
@stack('scripts')
</body>
</html>
