@extends('layouts.app')
@section('title', 'Semua Proyek')
@section('topbar-title', 'Semua Proyek')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px">
    <div>
        <h1 class="page-title">Semua Proyek</h1>
        <p class="page-desc">Daftar proyek yang bisa diakses.</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Proyek Baru
    </a>
    @endif
</div>

{{-- Filter status --}}
<div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;align-items:center">
    <span style="font-size:12px;color:var(--ink-muted);font-weight:600">Filter:</span>
    @foreach([''=>'Semua','Active'=>'Active','Draft'=>'Draft','Suspended'=>'Suspended','Completed'=>'Completed','Cancelled'=>'Cancelled'] as $val=>$label)
    @php $isActive = request('status')===$val || ($val==='' && !request('status')); @endphp
    <a href="{{ route('projects.index', $val ? ['status'=>$val] : []) }}"
       class="btn btn-utility btn-sm"
       style="{{ $isActive ? 'background:var(--primary);color:#fff;border-color:var(--primary)' : '' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="card">
    @if($projects->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
        <p>Tidak ada proyek{{ request('status') ? ' dengan status '.request('status') : '' }}.</p>
        <div style="margin-top:16px">
            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Buat Proyek Pertama
            </a>
        </div>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Proyek</th>
                    <th>Status</th>
                    <th>Anggaran</th>
                    <th>Progress</th>
                    <th>Mulai</th>
                    <th>Tenggat</th>
                    <th>Tim</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($projects as $p)
            @php
                $taskDone  = $p->tasks->where('status','Done')->count();
                $taskTotal = $p->tasks->count();
                $pct       = $taskTotal > 0 ? round($taskDone/$taskTotal*100) : 0;
            @endphp
            <tr>
                <td>
                    <span style="font-family:monospace;font-size:11px;background:var(--canvas-soft);
                        padding:2px 6px;border-radius:4px;color:var(--ink-muted)">
                        {{ $p->kode }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('projects.show', $p) }}"
                       style="font-weight:600;color:var(--ink);text-decoration:none">
                        {{ $p->nama }}
                    </a>
                    @if($p->deskripsi)
                    <div style="font-size:11px;color:var(--ink-faint);margin-top:2px">
                        {{ Str::limit($p->deskripsi, 55) }}
                    </div>
                    @endif
                </td>
                <td>@include('partials.project-status-badge', ['status'=>$p->status])</td>
                <td style="font-size:12px;white-space:nowrap">
                    Rp {{ number_format($p->anggaran,0,',','.') }}
                </td>
                <td style="min-width:110px">
                    <div style="display:flex;align-items:center;gap:5px">
                        <div class="progress-bar" style="flex:1">
                            <div class="progress-fill {{ $pct>=100?'success':($pct>=50?'':'warning') }}"
                                 style="width:{{ $pct }}%"></div>
                        </div>
                        <span style="font-size:11px;color:var(--ink-faint);white-space:nowrap">{{ $pct }}%</span>
                    </div>
                    <div style="font-size:11px;color:var(--ink-faint);margin-top:2px">
                        {{ $taskDone }}/{{ $taskTotal }} task
                    </div>
                </td>
                <td style="font-size:12px;color:var(--ink-muted);white-space:nowrap">
                    {{ $p->tanggal_mulai?->format('d M Y') ?? '—' }}
                </td>
                <td style="font-size:12px;white-space:nowrap">
                    @php $overdue = $p->tanggal_selesai && $p->tanggal_selesai->isPast() && $p->status !== 'Completed'; @endphp
                    <span style="color:{{ $overdue ? '#b91c1c' : 'var(--ink-muted)' }}">
                        {{ $p->tanggal_selesai?->format('d M Y') ?? '—' }}
                        @if($overdue) <i class="fas fa-clock" title="Terlambat"></i> @endif
                    </span>
                </td>
                <td style="font-size:12px;color:var(--ink-muted)">
                    {{ $p->members_count ?? $p->members->count() }} orang
                </td>
                <td>
                    <div style="display:flex;gap:4px;justify-content:flex-end">
                        <a href="{{ route('projects.show', $p) }}"
                           class="btn btn-icon" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('projects.edit', $p) }}"
                           class="btn btn-icon" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('projects.destroy', $p) }}"
                              onsubmit="return confirm('Yakin hapus proyek {{ addslashes($p->nama) }}? Semua data terkait akan ikut terhapus.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon" style="color:#ef4444" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding:10px 14px;font-size:12px;color:var(--ink-faint);border-top:1px solid var(--hairline)">
        {{ $projects->count() }} proyek ditampilkan
    </div>
    @endif
</div>
@endsection
