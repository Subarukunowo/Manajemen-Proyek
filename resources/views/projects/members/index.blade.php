@extends('layouts.app')
@section('title', 'Tim — '.$project->nama)
@section('topbar-title', 'Resource Management')

@section('content')
@php
$myUserId  = auth()->id();
$myRole    = $members->firstWhere('user_id', $myUserId)?->peran
             ?? ($project->created_by === $myUserId ? 'Owner' : null);
$isAdmin   = auth()->user()->isAdmin(); // system-level admin
$isPM      = $isAdmin || in_array($myRole, ['Owner', 'Project_Manager']);
@endphp

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none">
            <i class="fas fa-arrow-left"></i> {{ $project->nama }}
        </a>
        <h1 class="page-title" style="margin-top:4px">Anggota Tim</h1>
        <p class="page-desc">Peran & akses anggota dalam proyek <strong>{{ $project->kode }}</strong>.</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('projects.roles.index', $project) }}" class="btn btn-utility btn-sm">
            <i class="fas fa-tag"></i> Kelola Peran
        </a>
        @if($isAdmin)
        <button class="btn btn-primary" onclick="openModal('modalAdd')">
            <i class="fas fa-user-plus"></i> Tambah Anggota
        </button>
        @endif
    </div>
</div>

{{-- Role legend --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;align-items:center">
    <span style="font-size:12px;color:var(--ink-muted);font-weight:600">Peran:</span>
    @foreach(['Owner'=>['badge-purple','Akses penuh + hapus proyek'],
               'Project_Manager'=>['badge-blue','CRUD semua modul + kelola tim'],
               'Developer'=>['badge-teal','Update progress task sendiri'],
               'Auditor'=>['badge-orange','Hanya lihat (read-only)'],
               'Stakeholder'=>['badge-grey','Hanya lihat dashboard']] as $role=>[$cls,$desc])
    <span class="badge {{ $cls }}" title="{{ $desc }}" style="cursor:help">
        {{ str_replace('_',' ',$role) }}
    </span>
    @endforeach
</div>

{{-- Member list as table --}}
<div class="card">
    @if($members->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-users"></i></div>
        <p>Belum ada anggota tim.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th>Peran di Proyek</th>
                    <th>Hak Akses</th>
                    <th>Bergabung</th>
                    @if($isPM)<th style="text-align:right">Aksi</th>@endif                </tr>
            </thead>
            <tbody>
            @foreach($members->sortBy(fn($m) => match($m->peran) {
                'Owner'=>0,'Project_Manager'=>1,'Developer'=>2,'Auditor'=>3,default=>4}) as $mem)
            @php
            $peranColors = ['Owner'=>'badge-purple','Project_Manager'=>'badge-blue',
                            'Developer'=>'badge-teal','Auditor'=>'badge-orange','Stakeholder'=>'badge-grey'];
            $aksesMap = ['Owner'=>'Akses penuh','Project_Manager'=>'Kelola semua modul',
                         'Developer'=>'Update task sendiri','Auditor'=>'View only','Stakeholder'=>'View only'];
            $colors = ['#0075de','#2a9d99','#1aae39','#dd5b00','#7e22ce'];
            $bgColor = $colors[$mem->id % count($colors)];
            $isMe = $mem->user_id === $myUserId;
            @endphp
            <tr style="{{ $isMe ? 'background:rgba(0,117,222,.04)' : '' }}">
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:34px;height:34px;border-radius:50%;background:{{ $bgColor }};
                             display:flex;align-items:center;justify-content:center;
                             font-size:13px;font-weight:700;color:#fff;flex-shrink:0">
                            {{ strtoupper(substr($mem->user->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px">
                                {{ $mem->user->name ?? 'Unknown' }}
                                @if($isMe)<span style="font-size:11px;color:var(--primary);margin-left:4px">(Anda)</span>@endif
                            </div>
                            <div style="font-size:12px;color:var(--ink-faint)">{{ $mem->user->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $peranColors[$mem->peran]??'badge-grey' }}">
                        {{ str_replace('_',' ',$mem->peran) }}
                    </span>
                </td>
                <td style="font-size:12px;color:var(--ink-muted)">
                    {{ $aksesMap[$mem->peran] ?? '—' }}
                </td>
                <td style="font-size:12px;color:var(--ink-muted)">
                    {{ $mem->tanggal_bergabung?->format('d M Y') ?? '—' }}
                </td>
                @if($isPM)
                <td>
                    <div style="display:flex;gap:4px;justify-content:flex-end">
                        @if(!$isMe || $myRole === 'Owner')
                        <button class="btn btn-icon" onclick='openEdit({{ $mem->toJson() }})' title="Edit Peran">
                            <i class="fas fa-pencil"></i>
                        </button>
                        @endif
                        @if($mem->peran !== 'Owner')
                        <form method="POST" action="{{ route('projects.members.destroy',[$project,$mem]) }}"
                              onsubmit="return confirm('Hapus {{ addslashes($mem->user->name ?? '') }} dari proyek?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon" style="color:#ef4444" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
                @endif            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@if($isPM)
{{-- Modal Add — PM only --}}
<div class="modal-backdrop" id="modalAdd">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Tambah Anggota Tim</span>
            <button class="modal-close" onclick="closeModal('modalAdd')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.members.store',$project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Pengguna <span style="color:#ef4444">*</span></label>
                <select name="user_id" class="form-select" required>
                    <option value="">— Pilih pengguna —</option>
                    @foreach($users as $u)
                    @if(!$members->pluck('user_id')->contains($u->id))
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endif
                    @endforeach
                </select>
                @error('user_id')<span style="font-size:12px;color:#ef4444">{{ $message }}</span>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Peran <span style="color:#ef4444">*</span></label>
                    <select name="peran" class="form-select" required>
                        @foreach($roles as $r)
                        <option value="{{ $r->nama }}">{{ str_replace('_',' ',$r->nama) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Bergabung</label>
                    <input type="date" name="tanggal_bergabung" class="form-input" value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAdd')">Batal</button>
                <button type="submit" class="btn btn-primary">Tambahkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Role — PM only --}}
<div class="modal-backdrop" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Ubah Peran Anggota</span>
            <button class="modal-close" onclick="closeModal('modalEdit')">×</button>
        </div>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama</label>
                <div id="editMemberName" style="padding:8px 10px;background:var(--canvas-soft);
                     border:1px solid var(--hairline);border-radius:var(--r-xs);
                     font-size:14px;color:var(--ink-muted)">—</div>
            </div>
            <div class="form-group">
                <label class="form-label">Peran Baru <span style="color:#ef4444">*</span></label>
                <select name="peran" id="editPeran" class="form-select" required>
                    @foreach($roles as $r)
                    <option value="{{ $r->nama }}">{{ str_replace('_',' ',$r->nama) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Bergabung</label>
                <input type="date" name="tanggal_bergabung" id="editTgl" class="form-input">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function openEdit(m) {
    const nameEl = document.getElementById('editMemberName');
    if (nameEl) nameEl.textContent = m.user ? m.user.name : '—';
    document.getElementById('editPeran').value = m.peran;
    document.getElementById('editTgl').value   = m.tanggal_bergabung ? m.tanggal_bergabung.substring(0,10) : '';
    document.getElementById('formEdit').action = `/projects/{{ $project->id }}/members/${m.id}`;
    openModal('modalEdit');
}
</script>
@endpush
