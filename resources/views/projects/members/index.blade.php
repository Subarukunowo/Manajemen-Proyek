@extends('layouts.app')
@section('title', 'Tim — '.$project->nama)
@section('topbar-title', 'Manajemen Tim')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Anggota Tim</h1>
        <p class="page-desc">Kelola anggota dan peran dalam proyek.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')"><i class="fas fa-user-plus"></i> Tambah Anggota</button>
</div>

{{-- Member cards --}}
@if($members->isEmpty())
<div class="empty-state">
    <div class="empty-icon"><i class="fas fa-users"></i></div>
    <p>Belum ada anggota tim. Tambahkan anggota untuk proyek ini.</p>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:var(--sp-md)" class="section">
    @foreach($members as $mem)
    @php
    $peranColors = ['Owner'=>'badge-purple','Project_Manager'=>'badge-blue','Developer'=>'badge-teal','Auditor'=>'badge-orange','Stakeholder'=>'badge-grey'];
    $initials = strtoupper(substr($mem->user->name ?? '?', 0, 2));
    $colors   = ['#0075de','#2a9d99','#1aae39','#dd5b00','#7e22ce'];
    $bgColor  = $colors[$mem->id % count($colors)];
    @endphp
    <div class="card" style="display:flex;align-items:center;gap:var(--sp-md)">
        <div style="width:44px;height:44px;border-radius:var(--r-full);background:{{ $bgColor }};display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0">
            {{ $initials }}
        </div>
        <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:14px;color:var(--ink)">{{ $mem->user->name ?? 'Unknown' }}</div>
            <div style="font-size:12px;color:var(--ink-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $mem->user->email ?? '' }}</div>
            <div style="margin-top:4px"><span class="badge {{ $peranColors[$mem->peran]??'badge-grey' }}">{{ str_replace('_',' ',$mem->peran) }}</span></div>
            @if($mem->tanggal_bergabung)
            <div style="font-size:11px;color:var(--ink-faint);margin-top:3px"><i class="fas fa-calendar-alt"></i> {{ $mem->tanggal_bergabung->format('d M Y') }}</div>
            @endif
        </div>
        <div style="display:flex;flex-direction:column;gap:4px;flex-shrink:0">
            <button class="btn btn-icon" onclick='openEdit({{ $mem->toJson() }})' title="Edit"><i class="fas fa-pencil"></i></button>
            <form method="POST" action="{{ route('projects.members.destroy',[$project,$mem]) }}" onsubmit="return confirm('Hapus anggota ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-icon" style="color:#ef4444"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Modal Add --}}
<div class="modal-backdrop" id="modalAdd">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Tambah Anggota</span>
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
                        @foreach(['Owner','Project_Manager','Developer','Auditor','Stakeholder'] as $p)
                        <option value="{{ $p }}">{{ str_replace('_',' ',$p) }}</option>
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

{{-- Modal Edit --}}
<div class="modal-backdrop" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Peran Anggota</span>
            <button class="modal-close" onclick="closeModal('modalEdit')">×</button>
        </div>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Peran <span style="color:#ef4444">*</span></label>
                <select name="peran" id="editPeran" class="form-select" required>
                    @foreach(['Owner','Project_Manager','Developer','Auditor','Stakeholder'] as $p)
                    <option value="{{ $p }}">{{ str_replace('_',' ',$p) }}</option>
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
@endsection

@push('scripts')
<script>
function openEdit(m) {
    document.getElementById('editPeran').value = m.peran;
    document.getElementById('editTgl').value   = m.tanggal_bergabung ? m.tanggal_bergabung.substring(0,10) : '';
    document.getElementById('formEdit').action = `/projects/{{ $project->id }}/members/${m.id}`;
    openModal('modalEdit');
}
</script>
@endpush
