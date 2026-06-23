@extends('layouts.app')
@section('title', 'Milestone — '.$project->nama)
@section('topbar-title', 'Milestone')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Milestone</h1>
        <p class="page-desc">Target pencapaian kunci proyek.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')"><i class="fas fa-plus"></i> Tambah Milestone</button>
</div>

{{-- Stats --}}
<div class="stats-grid section">
    @php
    $achieved = $milestones->where('status','Achieved')->count();
    $delayed  = $milestones->where('status','Delayed')->count();
    $pending  = $milestones->where('status','Pending')->count();
    $total    = $milestones->count();
    @endphp
    <div class="stat-card stat-accent-green">
        <div class="stat-label">Achieved</div>
        <div class="stat-value">{{ $achieved }}</div>
        <div class="stat-sub">{{ $total>0?round($achieved/$total*100):0 }}% dari total</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value">{{ $pending }}</div>
    </div>
    <div class="stat-card" style="{{ $delayed>0?'border-color:#fecaca':'' }}">
        <div class="stat-label">Delayed</div>
        <div class="stat-value" style="{{ $delayed>0?'color:#b91c1c':'' }}">{{ $delayed }}</div>
    </div>
</div>

<div class="card">
    @if($milestones->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-flag"></i></div>
        <p>Belum ada milestone. Tambahkan target pencapaian proyek.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama Milestone</th><th>Target</th><th>Aktual</th><th>Status</th><th>Deviasi</th><th></th></tr></thead>
            <tbody>
            @foreach($milestones as $m)
            @php
            $sm = ['Pending'=>'badge-grey','Achieved'=>'badge-green','Delayed'=>'badge-red'];
            $dev = null;
            if ($m->tanggal_aktual && $m->tanggal_target) {
                $dev = $m->tanggal_aktual->diffInDays($m->tanggal_target, false);
            }
            @endphp
            <tr>
                <td style="font-weight:500">{{ $m->nama }}</td>
                <td style="font-size:13px">{{ $m->tanggal_target->format('d M Y') }}</td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $m->tanggal_aktual?->format('d M Y') ?? '—' }}</td>
                <td><span class="badge {{ $sm[$m->status]??'badge-grey' }}">{{ $m->status }}</span></td>
                <td style="font-size:13px">
                    @if($dev !== null)
                        <span style="color:{{ $dev>0?'#b91c1c':'var(--accent-green)' }}">
                            {{ $dev>0 ? '+'.$dev.' hari terlambat' : abs($dev).' hari lebih cepat' }}
                        </span>
                    @else
                        <span style="color:var(--ink-faint)">—</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:4px">
                        <button class="btn btn-icon" onclick='openEdit({{ $m->toJson() }})' title="Edit"><i class="fas fa-pencil"></i></button>
                        <form method="POST" action="{{ route('projects.milestones.destroy',[$project,$m]) }}" onsubmit="return confirm('Hapus milestone ini?')">
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
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Tambah Milestone</span>
            <button class="modal-close" onclick="closeModal('modalAdd')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.milestones.store',$project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Milestone <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" class="form-input" placeholder="Contoh: Go-Live Production" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Target Tanggal <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_target" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Aktual</label>
                    <input type="date" name="tanggal_aktual" class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option>Pending</option><option>Achieved</option><option>Delayed</option>
                </select>
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
            <span class="modal-title">Edit Milestone</span>
            <button class="modal-close" onclick="closeModal('modalEdit')">×</button>
        </div>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Milestone <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" id="editNama" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Target Tanggal <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_target" id="editTarget" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Aktual</label>
                    <input type="date" name="tanggal_aktual" id="editAktual" class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" id="editStatus" class="form-select">
                    <option>Pending</option><option>Achieved</option><option>Delayed</option>
                </select>
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
    document.getElementById('editNama').value   = m.nama;
    document.getElementById('editTarget').value = m.tanggal_target ? m.tanggal_target.substring(0,10) : '';
    document.getElementById('editAktual').value = m.tanggal_aktual ? m.tanggal_aktual.substring(0,10) : '';
    document.getElementById('editStatus').value = m.status;
    document.getElementById('formEdit').action  = `/projects/{{ $project->id }}/milestones/${m.id}`;
    openModal('modalEdit');
}
</script>
@endpush
