@extends('layouts.app')
@section('title', 'Risiko — '.$project->nama)
@section('topbar-title', 'Log Risiko')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Log Risiko</h1>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAddRisk')"><i class="fas fa-plus"></i> Tambah Risiko</button>
</div>

<!-- Risk matrix legend -->
<div style="display:flex;gap:var(--sp-sm);margin-bottom:var(--sp-md);flex-wrap:wrap;align-items:center">
    <span style="font-size:12px;color:var(--ink-muted)">Skor Risiko:</span>
    <span class="badge badge-green">1–2 Rendah</span>
    <span class="badge badge-yellow">3–4 Sedang</span>
    <span class="badge badge-orange">6 Tinggi</span>
    <span class="badge badge-red">9 Kritis</span>
</div>

<div class="card">
    @if($risks->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <p>Belum ada risiko tercatat untuk proyek ini.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th>Deskripsi Risiko</th><th>Probabilitas</th><th>Dampak</th><th>Skor</th><th>Status</th><th>PIC</th><th>Mitigasi</th><th></th></tr></thead>
            <tbody>
            @foreach($risks as $r)
            @php
            $skor = $r->skor_risiko;
            $skorBadge = $skor >= 9 ? 'badge-red' : ($skor >= 6 ? 'badge-orange' : ($skor >= 3 ? 'badge-yellow' : 'badge-green'));
            $sm = ['Identified'=>'badge-blue','Mitigated'=>'badge-teal','Occurred'=>'badge-red','Closed'=>'badge-grey'];
            $pm = ['Low'=>'badge-green','Medium'=>'badge-yellow','High'=>'badge-red'];
            @endphp
            <tr>
                <td style="font-weight:500;max-width:240px">{{ $r->deskripsi_risiko }}</td>
                <td><span class="badge {{ $pm[$r->probabilitas]??'badge-grey' }}">{{ $r->probabilitas }}</span></td>
                <td><span class="badge {{ $pm[$r->dampak]??'badge-grey' }}">{{ $r->dampak }}</span></td>
                <td><span class="badge {{ $skorBadge }}" style="font-size:13px;font-weight:700">{{ $skor }}</span></td>
                <td><span class="badge {{ $sm[$r->status]??'badge-grey' }}">{{ $r->status }}</span></td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $r->assignee?->name ?? '—' }}</td>
                <td style="font-size:13px;color:var(--ink-muted);max-width:200px">{{ Str::limit($r->mitigasi,60) ?? '—' }}</td>
                <td>
                    <div style="display:flex;gap:4px">
                        <button class="btn btn-icon" onclick='openEditRisk({{ $r->toJson() }})' title="Edit"><i class="fas fa-pencil"></i></button>
                        <form method="POST" action="{{ route('projects.risks.destroy', [$project, $r]) }}" onsubmit="return confirm('Hapus risiko ini?')">
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

<!-- Modal Add Risk -->
<div class="modal-backdrop" id="modalAddRisk">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Tambah Risiko</span>
            <button class="modal-close" onclick="closeModal('modalAddRisk')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.risks.store', $project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Deskripsi Risiko <span style="color:#ef4444">*</span></label>
                <textarea name="deskripsi_risiko" class="form-textarea" placeholder="Jelaskan potensi risiko..." required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Probabilitas</label>
                    <select name="probabilitas" class="form-select">
                        <option>Low</option><option selected>Medium</option><option>High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Dampak</label>
                    <select name="dampak" class="form-select">
                        <option>Low</option><option selected>Medium</option><option>High</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option>Identified</option><option>Mitigated</option><option>Occurred</option><option>Closed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Penanggung Jawab</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">— Tidak ditugaskan —</option>
                        @foreach($members as $m)
                        <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Mitigasi</label>
                <textarea name="mitigasi" class="form-textarea" placeholder="Langkah mitigasi risiko..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddRisk')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Risk -->
<div class="modal-backdrop" id="modalEditRisk">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Risiko</span>
            <button class="modal-close" onclick="closeModal('modalEditRisk')">×</button>
        </div>
        <form method="POST" id="formEditRisk">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Deskripsi Risiko <span style="color:#ef4444">*</span></label>
                <textarea name="deskripsi_risiko" id="editRiskDesk" class="form-textarea" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Probabilitas</label>
                    <select name="probabilitas" id="editRiskProb" class="form-select">
                        <option>Low</option><option>Medium</option><option>High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Dampak</label>
                    <select name="dampak" id="editRiskDampak" class="form-select">
                        <option>Low</option><option>Medium</option><option>High</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editRiskStatus" class="form-select">
                        <option>Identified</option><option>Mitigated</option><option>Occurred</option><option>Closed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Penanggung Jawab</label>
                    <select name="assigned_to" id="editRiskAssignee" class="form-select">
                        <option value="">— Tidak ditugaskan —</option>
                        @foreach($members as $m)
                        <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Mitigasi</label>
                <textarea name="mitigasi" id="editRiskMitigasi" class="form-textarea"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditRisk')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openEditRisk(r) {
    document.getElementById('editRiskDesk').value     = r.deskripsi_risiko;
    document.getElementById('editRiskProb').value     = r.probabilitas;
    document.getElementById('editRiskDampak').value   = r.dampak;
    document.getElementById('editRiskMitigasi').value = r.mitigasi ?? '';
    document.getElementById('editRiskStatus').value   = r.status;
    document.getElementById('editRiskAssignee').value = r.assigned_to ?? '';
    document.getElementById('formEditRisk').action    = `/projects/{{ $project->id }}/risks/${r.id}`;
    openModal('modalEditRisk');
}
</script>
@endpush
