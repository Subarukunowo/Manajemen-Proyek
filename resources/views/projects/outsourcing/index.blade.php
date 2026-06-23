@extends('layouts.app')
@section('title', 'Vendor — '.$project->nama)
@section('topbar-title', 'Outsourcing & Vendor')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Outsourcing & Vendor</h1>
        <p class="page-desc">Kontrak pihak ketiga yang terlibat dalam proyek.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')"><i class="fas fa-plus"></i> Tambah Vendor</button>
</div>

{{-- Stats --}}
@php
$totalKontrak = $vendors->sum('nilai_kontrak');
$active       = $vendors->where('status','Active')->count();
$avgProgress  = $vendors->isNotEmpty() ? round($vendors->avg('persen_selesai'),1) : 0;
@endphp
<div class="stats-grid section">
    <div class="stat-card stat-accent-blue">
        <div class="stat-label">Total Nilai Kontrak</div>
        <div class="stat-value" style="font-size:20px">Rp {{ number_format($totalKontrak,0,',','.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Vendor Aktif</div>
        <div class="stat-value">{{ $active }}</div>
        <div class="stat-sub">dari {{ $vendors->count() }} total</div>
    </div>
    <div class="stat-card stat-accent-teal">
        <div class="stat-label">Rata-rata Progress</div>
        <div class="stat-value">{{ $avgProgress }}%</div>
        <div style="margin-top:8px"><div class="progress-bar"><div class="progress-fill" style="width:{{ $avgProgress }}%"></div></div></div>
    </div>
</div>

<div class="card">
    @if($vendors->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-handshake"></i></div>
        <p>Belum ada vendor outsourcing tercatat.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama Vendor</th><th>Lingkup Kerja</th><th>Nilai Kontrak</th><th>Mulai</th><th>Selesai</th><th>Status</th><th>Progress</th><th></th></tr></thead>
            <tbody>
            @foreach($vendors as $v)
            @php $sm=['Active'=>'badge-blue','Completed'=>'badge-green','Terminated'=>'badge-red']; @endphp
            <tr>
                <td style="font-weight:500">{{ $v->nama_vendor }}</td>
                <td style="font-size:13px;color:var(--ink-muted);max-width:200px">{{ Str::limit($v->lingkup_kerja,50) ?? '—' }}</td>
                <td style="font-size:13px;font-weight:500">Rp {{ number_format($v->nilai_kontrak,0,',','.') }}</td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $v->tanggal_mulai->format('d M Y') }}</td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $v->tanggal_selesai->format('d M Y') }}</td>
                <td><span class="badge {{ $sm[$v->status]??'badge-grey' }}">{{ $v->status }}</span></td>
                <td style="min-width:110px">
                    <div style="display:flex;align-items:center;gap:6px">
                        <div class="progress-bar" style="flex:1"><div class="progress-fill {{ $v->persen_selesai>=100?'success':'' }}" style="width:{{ $v->persen_selesai }}%"></div></div>
                        <span style="font-size:12px;color:var(--ink-faint)">{{ $v->persen_selesai }}%</span>
                    </div>
                </td>
                <td>
                    <div style="display:flex;gap:4px">
                        <button class="btn btn-icon" onclick='openEdit({{ $v->toJson() }})' title="Edit"><i class="fas fa-pencil"></i></button>
                        <form method="POST" action="{{ route('projects.outsourcing.destroy',[$project,$v]) }}" onsubmit="return confirm('Hapus vendor ini?')">
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

{{-- Modal Add --}}
<div class="modal-backdrop" id="modalAdd">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">Tambah Vendor Outsourcing</span>
            <button class="modal-close" onclick="closeModal('modalAdd')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.outsourcing.store',$project) }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Vendor <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama_vendor" class="form-input" placeholder="PT. Contoh Vendor" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai Kontrak (Rp) <span style="color:#ef4444">*</span></label>
                    <input type="number" name="nilai_kontrak" class="form-input" min="0" step="1000" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Lingkup Kerja</label>
                <textarea name="lingkup_kerja" class="form-textarea" placeholder="Uraian pekerjaan yang dikontrakkan..."></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-input" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option>Active</option><option>Completed</option><option>Terminated</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Progress (%)</label>
                    <input type="number" name="persen_selesai" class="form-input" min="0" max="100" value="0">
                </div>
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
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">Edit Vendor</span>
            <button class="modal-close" onclick="closeModal('modalEdit')">×</button>
        </div>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Vendor <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama_vendor" id="editNama" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai Kontrak (Rp)</label>
                    <input type="number" name="nilai_kontrak" id="editNilai" class="form-input" min="0" step="1000">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Lingkup Kerja</label>
                <textarea name="lingkup_kerja" id="editLingkup" class="form-textarea"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="editMulai" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="editSelesai" class="form-input">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editStatus" class="form-select">
                        <option>Active</option><option>Completed</option><option>Terminated</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Progress (%)</label>
                    <input type="number" name="persen_selesai" id="editPersen" class="form-input" min="0" max="100">
                </div>
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
function openEdit(v) {
    document.getElementById('editNama').value    = v.nama_vendor;
    document.getElementById('editNilai').value   = v.nilai_kontrak;
    document.getElementById('editLingkup').value = v.lingkup_kerja ?? '';
    document.getElementById('editMulai').value   = v.tanggal_mulai   ? v.tanggal_mulai.substring(0,10)   : '';
    document.getElementById('editSelesai').value = v.tanggal_selesai ? v.tanggal_selesai.substring(0,10) : '';
    document.getElementById('editStatus').value  = v.status;
    document.getElementById('editPersen').value  = v.persen_selesai;
    document.getElementById('formEdit').action   = `/projects/{{ $project->id }}/outsourcing/${v.id}`;
    openModal('modalEdit');
}
</script>
@endpush
