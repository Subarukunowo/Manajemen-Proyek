@extends('layouts.app')
@section('title', 'Peran — '.$project->nama)
@section('topbar-title', 'Manajemen Peran')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.members.index', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none">
            <i class="fas fa-arrow-left"></i> Anggota Tim
        </a>
        <h1 class="page-title" style="margin-top:4px">Peran Proyek</h1>
        <p class="page-desc">
            Kelola peran kustom untuk proyek <strong>{{ $project->kode }}</strong>.
            Maks. <strong>15 peran</strong> per proyek.
        </p>
    </div>
    @if($isAdmin)
    <button class="btn btn-primary"
            onclick="openModal('modalAdd')"
            {{ $roles->count() >= 15 ? 'disabled title=Batas 15 peran tercapai' : '' }}
            style="{{ $roles->count() >= 15 ? 'opacity:.5;cursor:not-allowed' : '' }}">
        <i class="fas fa-plus"></i> Tambah Peran
    </button>
    @endif
</div>

{{-- Progress bar limit --}}
<div style="margin-bottom:20px">
    <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-muted);margin-bottom:4px">
        <span>{{ $roles->count() }} dari 15 peran digunakan</span>
        @if($roles->count() >= 15)
        <span style="color:#b91c1c;font-weight:600"><i class="fas fa-lock"></i> Batas tercapai</span>
        @endif
    </div>
    <div class="progress-bar">
        <div class="progress-fill {{ $roles->count() >= 15 ? 'danger' : ($roles->count() >= 12 ? 'warning' : '') }}"
             style="width:{{ ($roles->count()/15)*100 }}%"></div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-error" style="margin-bottom:16px">
    <i class="fas fa-circle-xmark"></i> {{ $errors->first() }}
</div>
@endif

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px"></th>
                    <th>Nama Peran</th>
                    <th>Deskripsi / Hak Akses</th>
                    <th>Warna</th>
                    <th>Tipe</th>
                    @if($isAdmin)<th style="text-align:right">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
            <tr>
                <td>
                    <div style="width:20px;height:20px;border-radius:4px;background:{{ $role->warna }}"></div>
                </td>
                <td>
                    <span style="display:inline-flex;align-items:center;gap:6px;font-weight:600;
                         padding:3px 10px;border-radius:99px;font-size:12px;
                         background:{{ $role->warna }}20;color:{{ $role->warna }}">
                        {{ str_replace('_', ' ', $role->nama) }}
                    </span>
                </td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $role->deskripsi ?? '—' }}</td>
                <td style="font-family:monospace;font-size:12px;color:var(--ink-muted)">{{ $role->warna }}</td>
                <td>
                    @if($role->is_default)
                    <span class="badge badge-grey">Default</span>
                    @else
                    <span class="badge badge-blue">Kustom</span>
                    @endif
                </td>
                @if($isAdmin)
                <td>
                    <div style="display:flex;gap:4px;justify-content:flex-end">
                        <button class="btn btn-icon" onclick='openEdit({{ $role->toJson() }})' title="Edit">
                            <i class="fas fa-pencil"></i>
                        </button>
                        @if(!$role->is_default)
                        <form method="POST" action="{{ route('projects.roles.destroy', [$project, $role]) }}"
                              onsubmit="return confirm({{ Js::from('Hapus peran '.str_replace('_',' ',$role->nama).'?') }})">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon" style="color:#ef4444" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @else
                        <button class="btn btn-icon" disabled title="Peran default tidak dapat dihapus"
                                style="opacity:.3;cursor:not-allowed">
                            <i class="fas fa-lock"></i>
                        </button>
                        @endif
                    </div>
                </td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($isAdmin)
{{-- Modal Add --}}
<div class="modal-backdrop" id="modalAdd">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Tambah Peran Baru</span>
            <button class="modal-close" onclick="closeModal('modalAdd')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.roles.store', $project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Peran <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" class="form-input"
                       placeholder="Contoh: QA Engineer" maxlength="50" required>
                <span style="font-size:11px;color:var(--ink-faint)">Tanpa spasi di tengah jika multi-kata (gunakan _ atau CamelCase)</span>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Warna Badge <span style="color:#ef4444">*</span></label>
                    <div style="display:flex;gap:8px;align-items:center">
                        <input type="color" name="warna" id="addWarna" value="#0075de"
                               style="width:40px;height:34px;padding:2px;border:1px solid #ddd;border-radius:4px;cursor:pointer">
                        <input type="text" id="addWarnaText" value="#0075de"
                               style="flex:1;padding:8px 10px;font-family:monospace;font-size:13px;
                                      border:1px solid #ddd;border-radius:4px;outline:none"
                               oninput="document.getElementById('addWarna').value=this.value"
                               placeholder="#000000">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Preview</label>
                    <div id="addPreview" style="padding:4px 12px;border-radius:99px;font-size:12px;
                         font-weight:600;display:inline-block;background:#0075de20;color:#0075de">
                        Preview Peran
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi / Hak Akses</label>
                <input type="text" name="deskripsi" class="form-input"
                       placeholder="Contoh: Mengelola pengujian dan QA" maxlength="255">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAdd')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-backdrop" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Peran</span>
            <button class="modal-close" onclick="closeModal('modalEdit')">×</button>
        </div>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Peran</label>
                <input type="text" name="nama" id="editNama" class="form-input" maxlength="50" required>
                <span id="editNamaNote" style="font-size:11px;color:var(--ink-faint);display:none">
                    <i class="fas fa-lock"></i> Nama peran default tidak dapat diubah
                </span>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Warna Badge</label>
                    <div style="display:flex;gap:8px;align-items:center">
                        <input type="color" name="warna" id="editWarna"
                               style="width:40px;height:34px;padding:2px;border:1px solid #ddd;border-radius:4px;cursor:pointer"
                               oninput="document.getElementById('editWarnaText').value=this.value;updateEditPreview()">
                        <input type="text" id="editWarnaText"
                               style="flex:1;padding:8px 10px;font-family:monospace;font-size:13px;
                                      border:1px solid #ddd;border-radius:4px;outline:none"
                               oninput="document.getElementById('editWarna').value=this.value;updateEditPreview()">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Preview</label>
                    <div id="editPreview" style="padding:4px 12px;border-radius:99px;font-size:12px;font-weight:600;display:inline-block"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <input type="text" name="deskripsi" id="editDeskripsi" class="form-input" maxlength="255">
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
// Sync color picker dan text input untuk Add modal
document.getElementById('addWarna')?.addEventListener('input', function() {
    document.getElementById('addWarnaText').value = this.value;
    updateAddPreview();
});
function updateAddPreview() {
    const color = document.getElementById('addWarna').value;
    const p = document.getElementById('addPreview');
    p.style.background = color + '20';
    p.style.color = color;
}

function openEdit(role) {
    document.getElementById('editNama').value       = role.nama;
    document.getElementById('editWarna').value      = role.warna;
    document.getElementById('editWarnaText').value  = role.warna;
    document.getElementById('editDeskripsi').value  = role.deskripsi ?? '';
    document.getElementById('formEdit').action      = `/projects/{{ $project->id }}/roles/${role.id}`;

    // Lock nama jika default
    const namaInput = document.getElementById('editNama');
    const namaNote  = document.getElementById('editNamaNote');
    namaInput.readOnly = role.is_default;
    namaNote.style.display = role.is_default ? 'block' : 'none';

    updateEditPreview();
    openModal('modalEdit');
}

function updateEditPreview() {
    const color = document.getElementById('editWarna').value;
    const nama  = document.getElementById('editNama').value || 'Preview';
    const p = document.getElementById('editPreview');
    p.style.background = color + '20';
    p.style.color = color;
    p.textContent = nama.replace('_', ' ');
}
</script>
@endpush