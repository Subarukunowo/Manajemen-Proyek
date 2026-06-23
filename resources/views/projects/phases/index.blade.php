@extends('layouts.app')
@section('title', 'Fase — '.$project->nama)
@section('topbar-title', 'Fase & WBS')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Fase & WBS</h1>
        <p class="page-desc">Work Breakdown Structure — urutan fase proyek.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAddPhase')"><i class="fas fa-plus"></i> Tambah Fase</button>
</div>

<div class="card">
    @if($phases->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-layer-group"></i></div>
        <p>Belum ada fase. Tambahkan fase pertama proyek ini.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th width="50">Urutan</th><th>Nama Fase</th><th>Status</th><th>Mulai</th><th>Selesai</th><th>Task</th><th></th></tr></thead>
            <tbody>
            @foreach($phases->sortBy('urutan') as $ph)
            <tr>
                <td style="text-align:center;color:var(--ink-faint)">{{ $ph->urutan }}</td>
                <td style="font-weight:500">{{ $ph->nama }}</td>
                <td>
                    @php $m=['Pending'=>'badge-grey','On_Progress'=>'badge-blue','Completed'=>'badge-green']; @endphp
                    <span class="badge {{ $m[$ph->status]??'badge-grey' }}">{{ $ph->status }}</span>
                </td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $ph->tanggal_mulai?->format('d M Y') ?? '—' }}</td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $ph->tanggal_selesai?->format('d M Y') ?? '—' }}</td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $ph->tasks->count() }}</td>
                <td>
                    <div style="display:flex;gap:4px">
                        <button class="btn btn-icon" onclick='openEditPhase({{ $ph->toJson() }})' title="Edit"><i class="fas fa-pencil"></i></button>
                        <form method="POST" action="{{ route('projects.phases.destroy', [$project, $ph]) }}" onsubmit="return confirm('Hapus fase ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon" style="color:#ef4444"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Modal: Add Phase -->
<div class="modal-backdrop" id="modalAddPhase">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Tambah Fase</span>
            <button class="modal-close" onclick="closeModal('modalAddPhase')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.phases.store', $project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Fase <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" class="form-input" placeholder="Contoh: Inisiasi" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan" class="form-input" min="1" placeholder="Otomatis jika kosong">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option>Pending</option><option>On_Progress</option><option>Completed</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddPhase')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Phase -->
<div class="modal-backdrop" id="modalEditPhase">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Fase</span>
            <button class="modal-close" onclick="closeModal('modalEditPhase')">×</button>
        </div>
        <form method="POST" id="formEditPhase">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Fase <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" id="editPhaseName" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan" id="editPhaseUrutan" class="form-input" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editPhaseStatus" class="form-select">
                        <option>Pending</option><option>On_Progress</option><option>Completed</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="editPhaseStart" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="editPhaseEnd" class="form-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditPhase')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditPhase(ph) {
    document.getElementById('editPhaseName').value   = ph.nama;
    document.getElementById('editPhaseUrutan').value = ph.urutan ?? '';
    document.getElementById('editPhaseStart').value  = ph.tanggal_mulai   ? ph.tanggal_mulai.substring(0,10)   : '';
    document.getElementById('editPhaseEnd').value    = ph.tanggal_selesai ? ph.tanggal_selesai.substring(0,10) : '';
    document.getElementById('editPhaseStatus').value = ph.status;
    document.getElementById('formEditPhase').action  = `/projects/{{ $project->id }}/phases/${ph.id}`;
    openModal('modalEditPhase');
}
</script>
@endpush
