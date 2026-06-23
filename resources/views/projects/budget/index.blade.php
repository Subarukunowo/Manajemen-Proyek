@extends('layouts.app')
@section('title', 'Anggaran — '.$project->nama)
@section('topbar-title', 'Anggaran')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Anggaran</h1>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAddBudget')"><i class="fas fa-plus"></i> Tambah Pos</button>
</div>

<!-- Summary cards -->
<div class="stats-grid section">
    <div class="stat-card stat-accent-blue">
        <div class="stat-label">Total Anggaran</div>
        <div class="stat-value" style="font-size:22px">Rp {{ number_format($summary['total_anggaran'],0,',','.') }}</div>
    </div>
    <div class="stat-card stat-accent-teal">
        <div class="stat-label">Total Realisasi</div>
        <div class="stat-value" style="font-size:22px">Rp {{ number_format($summary['total_realisasi'],0,',','.') }}</div>
    </div>
    <div class="stat-card {{ $summary['variansi'] >= 0 ? 'stat-accent-green' : '' }}" style="{{ $summary['variansi'] < 0 ? '--stat-color:#b91c1c' : '' }}">
        <div class="stat-label">Variansi</div>
        <div class="stat-value" style="font-size:22px;color:{{ $summary['variansi'] >= 0 ? 'var(--accent-green)' : '#b91c1c' }}">
            Rp {{ number_format(abs($summary['variansi']),0,',','.') }}
            <span style="font-size:14px;font-weight:400">{{ $summary['variansi'] >= 0 ? 'surplus' : 'defisit' }}</span>
        </div>
    </div>
    @php $pctBudget = $summary['total_anggaran'] > 0 ? round($summary['total_realisasi']/$summary['total_anggaran']*100,1) : 0; @endphp
    <div class="stat-card">
        <div class="stat-label">Penyerapan</div>
        <div class="stat-value" style="font-size:22px">{{ $pctBudget }}%</div>
        <div style="margin-top:8px"><div class="progress-bar"><div class="progress-fill {{ $pctBudget>100?'danger':($pctBudget>80?'warning':'') }}" style="width:{{ min($pctBudget,100) }}%"></div></div></div>
    </div>
</div>

<div class="card">
    @if($budgets->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-wallet"></i></div>
        <p>Belum ada pos anggaran. Tambahkan rincian anggaran proyek.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kategori</th><th>Anggaran</th><th>Realisasi</th><th>Variansi</th><th>Penyerapan</th><th>Keterangan</th><th></th></tr></thead>
            <tbody>
            @foreach($budgets as $b)
            @php $var = $b->anggaran - $b->realisasi; $pct = $b->anggaran > 0 ? round($b->realisasi/$b->anggaran*100,1) : 0; @endphp
            <tr>
                <td style="font-weight:500">{{ $b->kategori }}</td>
                <td style="font-size:13px">Rp {{ number_format($b->anggaran,0,',','.') }}</td>
                <td style="font-size:13px">Rp {{ number_format($b->realisasi,0,',','.') }}</td>
                <td style="font-size:13px;color:{{ $var>=0?'var(--accent-green)':'#b91c1c' }}">
                    {{ $var>=0?'+':'' }}Rp {{ number_format(abs($var),0,',','.') }}
                </td>
                <td style="min-width:100px">
                    <div style="display:flex;align-items:center;gap:6px">
                        <div class="progress-bar" style="flex:1"><div class="progress-fill {{ $pct>100?'danger':($pct>80?'warning':'') }}" style="width:{{ min($pct,100) }}%"></div></div>
                        <span style="font-size:12px;color:var(--ink-faint)">{{ $pct }}%</span>
                    </div>
                </td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $b->keterangan ?? '—' }}</td>
                <td>
                    <div style="display:flex;gap:4px">
                        <button class="btn btn-icon" onclick='openEditBudget({{ $b->toJson() }})' title="Edit"><i class="fas fa-pencil"></i></button>
                        <form method="POST" action="{{ route('projects.budget.destroy', [$project, $b]) }}" onsubmit="return confirm('Hapus pos ini?')">
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

<!-- Modal Add -->
<div class="modal-backdrop" id="modalAddBudget">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Tambah Pos Anggaran</span>
            <button class="modal-close" onclick="closeModal('modalAddBudget')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.budget.store', $project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Kategori <span style="color:#ef4444">*</span></label>
                <input type="text" name="kategori" class="form-input" placeholder="Contoh: Operasional, Hardware" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Anggaran (Rp)</label>
                    <input type="number" name="anggaran" class="form-input" min="0" step="1000" placeholder="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi (Rp)</label>
                    <input type="number" name="realisasi" class="form-input" min="0" step="1000" value="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" class="form-input" placeholder="Opsional">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddBudget')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-backdrop" id="modalEditBudget">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Pos Anggaran</span>
            <button class="modal-close" onclick="closeModal('modalEditBudget')">×</button>
        </div>
        <form method="POST" id="formEditBudget">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Kategori <span style="color:#ef4444">*</span></label>
                <input type="text" name="kategori" id="editBudgetKategori" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Anggaran (Rp)</label>
                    <input type="number" name="anggaran" id="editBudgetAnggaran" class="form-input" min="0" step="1000">
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi (Rp)</label>
                    <input type="number" name="realisasi" id="editBudgetRealisasi" class="form-input" min="0" step="1000">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" id="editBudgetKet" class="form-input">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditBudget')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openEditBudget(b) {
    document.getElementById('editBudgetKategori').value  = b.kategori;
    document.getElementById('editBudgetAnggaran').value  = b.anggaran;
    document.getElementById('editBudgetRealisasi').value = b.realisasi;
    document.getElementById('editBudgetKet').value       = b.keterangan ?? '';
    document.getElementById('formEditBudget').action     = `/projects/{{ $project->id }}/budget/${b.id}`;
    openModal('modalEditBudget');
}
</script>
@endpush
