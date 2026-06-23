@extends('layouts.app')
@section('title', 'Change Request — '.$project->nama)
@section('topbar-title', 'Change Request')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Change Request</h1>
        <p class="page-desc">Kelola permintaan perubahan lingkup proyek.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAddCR')"><i class="fas fa-plus"></i> Ajukan CR</button>
</div>

<div class="card">
    @if($changes->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-code-branch"></i></div>
        <p>Belum ada change request untuk proyek ini.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nomor CR</th><th>Judul</th><th>Dampak</th><th>Biaya Perubahan</th><th>Status</th><th>Tgl Request</th><th></th></tr></thead>
            <tbody>
            @foreach($changes as $cr)
            @php
            $sm=['Draft'=>'badge-grey','Pending'=>'badge-yellow','Approved'=>'badge-green','Rejected'=>'badge-red'];
            $dm=['Low'=>'badge-green','Medium'=>'badge-yellow','High'=>'badge-red'];
            @endphp
            <tr>
                <td><span style="font-family:monospace;font-size:12px;color:var(--ink-muted)">{{ $cr->nomor_cr }}</span></td>
                <td>
                    <div style="font-weight:500">{{ $cr->judul }}</div>
                    @if($cr->deskripsi)<div style="font-size:12px;color:var(--ink-faint)">{{ Str::limit($cr->deskripsi,50) }}</div>@endif
                </td>
                <td><span class="badge {{ $dm[$cr->dampak]??'badge-grey' }}">{{ $cr->dampak }}</span></td>
                <td style="font-size:13px">{{ $cr->biaya_perubahan ? 'Rp '.number_format($cr->biaya_perubahan,0,',','.') : '—' }}</td>
                <td><span class="badge {{ $sm[$cr->status]??'badge-grey' }}">{{ $cr->status }}</span></td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $cr->tanggal_request?->format('d M Y') }}</td>
                <td>
                    <div style="display:flex;gap:4px;flex-wrap:nowrap">
                        @if($cr->status === 'Pending')
                        <form method="POST" action="{{ route('projects.changes.approve', [$project, $cr]) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-utility btn-sm" style="color:var(--accent-green);border-color:var(--accent-green)" title="Approve">✓</button>
                        </form>
                        <form method="POST" action="{{ route('projects.changes.reject', [$project, $cr]) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-utility btn-sm" style="color:#ef4444;border-color:#ef4444" title="Reject">✗</button>
                        </form>
                        @endif
                        <button class="btn btn-icon" onclick='openEditCR({{ $cr->toJson() }})' title="Edit"><i class="fas fa-pencil"></i></button>
                        <form method="POST" action="{{ route('projects.changes.destroy', [$project, $cr]) }}" onsubmit="return confirm('Hapus CR ini?')">
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

<!-- Modal Add CR -->
<div class="modal-backdrop" id="modalAddCR">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">Ajukan Change Request</span>
            <button class="modal-close" onclick="closeModal('modalAddCR')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.changes.store', $project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Judul <span style="color:#ef4444">*</span></label>
                <input type="text" name="judul" class="form-input" placeholder="Judul perubahan..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea" placeholder="Detail perubahan yang diminta..."></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Dampak</label>
                    <select name="dampak" class="form-select">
                        <option>Low</option><option selected>Medium</option><option>High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Biaya Perubahan (Rp)</label>
                    <input type="number" name="biaya_perubahan" class="form-input" min="0" step="1000" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddCR')">Batal</button>
                <button type="submit" class="btn btn-primary">Ajukan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit CR -->
<div class="modal-backdrop" id="modalEditCR">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">Edit Change Request</span>
            <button class="modal-close" onclick="closeModal('modalEditCR')">×</button>
        </div>
        <form method="POST" id="formEditCR">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Judul <span style="color:#ef4444">*</span></label>
                <input type="text" name="judul" id="editCRJudul" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="editCRDesk" class="form-textarea"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Dampak</label>
                    <select name="dampak" id="editCRDampak" class="form-select">
                        <option>Low</option><option>Medium</option><option>High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editCRStatus" class="form-select">
                        <option>Draft</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Biaya Perubahan (Rp)</label>
                <input type="number" name="biaya_perubahan" id="editCRBiaya" class="form-input" min="0" step="1000">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditCR')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openEditCR(cr) {
    document.getElementById('editCRJudul').value  = cr.judul;
    document.getElementById('editCRDesk').value   = cr.deskripsi ?? '';
    document.getElementById('editCRDampak').value = cr.dampak;
    document.getElementById('editCRStatus').value = cr.status;
    document.getElementById('editCRBiaya').value  = cr.biaya_perubahan ?? 0;
    document.getElementById('formEditCR').action  = `/projects/{{ $project->id }}/changes/${cr.id}`;
    openModal('modalEditCR');
}
</script>
@endpush
