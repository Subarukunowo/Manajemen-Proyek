@extends('layouts.app')
@section('title', 'Resource — '.$project->nama)
@section('topbar-title', 'Resource')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none">
            <i class="fas fa-arrow-left"></i> {{ $project->nama }}
        </a>
        <h1 class="page-title" style="margin-top:4px">Resource & Alokasi</h1>
        <p class="page-desc">SDM, material, dan peralatan yang dialokasikan ke proyek.</p>
        @if(!$isAdmin)
        <div style="margin-top:6px;display:inline-flex;align-items:center;gap:6px;
             background:#dbeafe;color:#1d4ed8;padding:4px 10px;border-radius:var(--r-full);font-size:12px">
            <i class="fas fa-eye"></i> Mode lihat saja — hanya Admin yang dapat mengelola resource
        </div>
        @endif
    </div>
    @if($isAdmin)
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah Resource
    </button>
    @endif
</div>

{{-- Summary cards --}}
<div class="stats-grid section">
    <div class="stat-card stat-accent-blue">
        <div class="stat-label">Total Biaya Resource</div>
        <div class="stat-value" style="font-size:20px">Rp {{ number_format($summary['total_biaya'],0,',','.') }}</div>
    </div>
    @foreach(['Human'=>['icon'=>'user','color'=>'var(--accent-sky)'],
              'Material'=>['icon'=>'box','color'=>'var(--accent-orange)'],
              'Equipment'=>['icon'=>'tools','color'=>'var(--accent-teal)']] as $tipe=>$cfg)
    @php $g = $summary['by_tipe'][$tipe] ?? ['count'=>0,'total_biaya'=>0]; @endphp
    <div class="stat-card">
        <div class="stat-label" style="color:{{ $cfg['color'] }}">
            <i class="fas fa-{{ $cfg['icon'] }}"></i> {{ $tipe }}
        </div>
        <div class="stat-value" style="font-size:18px">{{ $g['count'] }} item</div>
        <div class="stat-sub">Rp {{ number_format($g['total_biaya'],0,',','.') }}</div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="card">
    @if($resources->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-boxes"></i></div>
        <p>Belum ada resource tercatat untuk proyek ini.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Resource</th><th>Tipe</th><th>Task</th>
                    <th>Jumlah</th><th>Satuan</th><th>Biaya/Satuan</th>
                    <th>Total Biaya</th><th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($resources as $r)
            @php $tm=['Human'=>'badge-blue','Material'=>'badge-orange','Equipment'=>'badge-teal']; @endphp
            <tr>
                <td style="font-weight:500">{{ $r->nama_resource }}</td>
                <td><span class="badge {{ $tm[$r->tipe]??'badge-grey' }}">{{ $r->tipe }}</span></td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $r->task?->nama ?? '—' }}</td>
                <td style="font-size:13px">{{ number_format($r->jumlah,2) }}</td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $r->satuan }}</td>
                <td style="font-size:13px">Rp {{ number_format($r->biaya_satuan,0,',','.') }}</td>
                <td style="font-size:13px;font-weight:600;color:var(--primary)">
                    Rp {{ number_format($r->total_biaya,0,',','.') }}
                </td>
                <td>
                    @if($isAdmin)
                    <div style="display:flex;gap:4px">
                        <button class="btn btn-icon" onclick='openEdit({{ $r->toJson() }})' title="Edit">
                            <i class="fas fa-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('projects.resources.destroy',[$project,$r]) }}"
                              onsubmit="return confirm('Hapus resource ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon" style="color:#ef4444"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    @else
                    <span style="font-size:11px;color:var(--ink-faint)">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@if($isAdmin)
{{-- Modal Add --}}
<div class="modal-backdrop" id="modalAdd">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">Tambah Resource</span>
            <button class="modal-close" onclick="closeModal('modalAdd')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.resources.store',$project) }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Resource <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama_resource" class="form-input" placeholder="Nama SDM / Material / Alat" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe <span style="color:#ef4444">*</span></label>
                    <select name="tipe" class="form-select" required>
                        <option>Human</option><option>Material</option><option>Equipment</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Task Terkait</label>
                <select name="task_id" class="form-select">
                    <option value="">— Tidak terkait task —</option>
                    @foreach($tasks as $t)<option value="{{ $t->id }}">{{ $t->nama }}</option>@endforeach
                </select>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Jumlah <span style="color:#ef4444">*</span></label>
                    <input type="number" name="jumlah" class="form-input" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan <span style="color:#ef4444">*</span></label>
                    <input type="text" name="satuan" class="form-input" placeholder="Jam, Pcs, Unit" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Biaya/Satuan (Rp) <span style="color:#ef4444">*</span></label>
                    <input type="number" name="biaya_satuan" class="form-input" min="0" step="1000" required>
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
            <span class="modal-title">Edit Resource</span>
            <button class="modal-close" onclick="closeModal('modalEdit')">×</button>
        </div>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Resource <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama_resource" id="editNama" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" id="editTipe" class="form-select">
                        <option>Human</option><option>Material</option><option>Equipment</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Task Terkait</label>
                <select name="task_id" id="editTask" class="form-select">
                    <option value="">— Tidak terkait task —</option>
                    @foreach($tasks as $t)<option value="{{ $t->id }}">{{ $t->nama }}</option>@endforeach
                </select>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" id="editJumlah" class="form-input" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="satuan" id="editSatuan" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Biaya/Satuan (Rp)</label>
                    <input type="number" name="biaya_satuan" id="editBiaya" class="form-input" min="0" step="1000">
                </div>
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
@if($isAdmin)
<script>
function openEdit(r) {
    document.getElementById('editNama').value   = r.nama_resource;
    document.getElementById('editTipe').value   = r.tipe;
    document.getElementById('editTask').value   = r.task_id ?? '';
    document.getElementById('editJumlah').value = r.jumlah;
    document.getElementById('editSatuan').value = r.satuan;
    document.getElementById('editBiaya').value  = r.biaya_satuan;
    document.getElementById('formEdit').action  = `/projects/{{ $project->id }}/resources/${r.id}`;
    openModal('modalEdit');
}
</script>
@endif
@endpush
