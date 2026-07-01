@extends('layouts.app')
@section('title', 'Kurva-S — '.$project->nama)
@section('topbar-title', 'Kurva-S')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Kurva-S Progress</h1>
        <p class="page-desc">Perbandingan target rencana vs realisasi kumulatif.</p>
    </div>
    <div style="display:flex;gap:var(--sp-sm)">
        <button class="btn btn-secondary" onclick="openModal('modalSnapshot')"><i class="fas fa-camera"></i> Auto Snapshot</button>
        <button class="btn btn-primary" onclick="openModal('modalAddKurva')"><i class="fas fa-plus"></i> Input Manual</button>
    </div>
</div>

<!-- Chart -->
<div class="card section" style="padding:var(--sp-lg)">
    @if($records->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-chart-line"></i></div>
        <p>Belum ada data Kurva-S. Tambahkan data periode pertama.</p>
    </div>
    @else
    <div style="position:relative;height:360px">
        <canvas id="kurvaSChart"></canvas>
    </div>
    @endif
</div>

<!-- Latest snapshot summary -->
@if($latest)
<div class="stats-grid section">
    <div class="stat-card stat-accent-blue">
        <div class="stat-label">Kumulatif Rencana</div>
        <div class="stat-value">{{ number_format($latest->rencana_kumulatif,2) }}%</div>
        <div class="stat-sub">Per {{ $latest->periode?->format('d M Y') }}</div>
    </div>
    <div class="stat-card stat-accent-teal">
        <div class="stat-label">Kumulatif Aktual</div>
        <div class="stat-value">{{ number_format($latest->realisasi_kumulatif,2) }}%</div>
    </div>
    @php $dev = round($latest->realisasi_kumulatif - $latest->rencana_kumulatif, 2); @endphp
    <div class="stat-card">
        <div class="stat-label">Deviasi</div>
        <div class="stat-value" style="color:{{ $dev >= 0 ? 'var(--accent-green)' : '#ef4444' }}">
            {{ $dev >= 0 ? '+' : '' }}{{ $dev }}%
        </div>
        <div class="stat-sub">{{ $dev >= 0 ? 'Ahead of schedule' : 'Behind schedule' }}</div>
    </div>
</div>
@endif

<!-- Data table -->
<div class="section">
    <div class="section-header">
        <span class="section-title">Riwayat Data</span>
    </div>
    <div class="card">
        @if($records->isEmpty())
        <div class="empty-state" style="padding:var(--sp-lg)">
            <p>Belum ada data.</p>
        </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Periode</th><th>Rencana Periode</th><th>Rencana Kumulatif</th><th>Realisasi Periode</th><th>Realisasi Kumulatif</th><th>Deviasi</th><th></th></tr>
                </thead>
                <tbody>
                @foreach($records->sortByDesc('periode') as $r)
                @php $d = round($r->realisasi_kumulatif - $r->rencana_kumulatif, 2); @endphp
                <tr>
                    <td style="font-weight:500">{{ $r->periode?->format('d M Y') }}</td>
                    <td style="font-size:13px">{{ number_format($r->rencana_periode,2) }}%</td>
                    <td style="font-size:13px;font-weight:600;color:var(--primary)">{{ number_format($r->rencana_kumulatif,2) }}%</td>
                    <td style="font-size:13px">{{ number_format($r->realisasi_periode,2) }}%</td>
                    <td style="font-size:13px;font-weight:600;color:var(--accent-teal)">{{ number_format($r->realisasi_kumulatif,2) }}%</td>
                    <td style="font-size:13px;font-weight:600;color:{{ $d>=0?'var(--accent-green)':'#ef4444' }}">{{ $d>=0?'+':'' }}{{ $d }}%</td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <button class="btn btn-icon" onclick='openEditKurva({{ $r->toJson() }})' title="Edit"><i class="fas fa-pencil"></i></button>
                            <form method="POST" action="{{ route('projects.kurvas.destroy', [$project, $r]) }}" onsubmit="return confirm('Hapus data ini?')">
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
</div>

<!-- Modal Manual Input -->
<div class="modal-backdrop" id="modalAddKurva">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Input Manual Kurva-S</span>
            <button class="modal-close" onclick="closeModal('modalAddKurva')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.kurvas.store', $project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Periode <span style="color:#ef4444">*</span></label>
                <input type="date" name="periode" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Rencana Kumulatif (%)</label>
                    <input type="number" name="rencana_kumulatif" class="form-input" min="0" max="100" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi Kumulatif (%)</label>
                    <input type="number" name="realisasi_kumulatif" class="form-input" min="0" max="100" step="0.01" placeholder="0.00">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Rencana Periode (%)</label>
                    <input type="number" name="rencana_periode" class="form-input" min="0" max="100" step="0.01" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi Periode (%)</label>
                    <input type="number" name="realisasi_periode" class="form-input" min="0" max="100" step="0.01" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddKurva')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Snapshot -->
<div class="modal-backdrop" id="modalSnapshot">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Auto Snapshot</span>
            <button class="modal-close" onclick="closeModal('modalSnapshot')">×</button>
        </div>
        <p style="font-size:14px;color:var(--ink-muted);margin-bottom:var(--sp-md)">
            Sistem akan menghitung realisasi kumulatif otomatis dari rata-rata progress semua task.
        </p>
        <form method="POST" action="{{ route('projects.kurvas.snapshot', $project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Periode <span style="color:#ef4444">*</span></label>
                <input type="date" name="periode" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Rencana Kumulatif (%) <span style="color:#ef4444">*</span></label>
                <input type="number" name="rencana_kumulatif" class="form-input" min="0" max="100" step="0.01" placeholder="Target %" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalSnapshot')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-camera"></i> Ambil Snapshot</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kurva-S -->
<div class="modal-backdrop" id="modalEditKurva">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Data Kurva-S</span>
            <button class="modal-close" onclick="closeModal('modalEditKurva')">×</button>
        </div>
        <form method="POST" id="formEditKurva">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Periode <span style="color:#ef4444">*</span></label>
                <input type="date" name="periode" id="editKurvaPeriode" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Rencana Kumulatif (%)</label>
                    <input type="number" name="rencana_kumulatif" id="editKurvaRencanaKum" class="form-input" min="0" max="100" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi Kumulatif (%)</label>
                    <input type="number" name="realisasi_kumulatif" id="editKurvaRealisasiKum" class="form-input" min="0" max="100" step="0.01">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Rencana Periode (%)</label>
                    <input type="number" name="rencana_periode" id="editKurvaRencanaPer" class="form-input" min="0" max="100" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi Periode (%)</label>
                    <input type="number" name="realisasi_periode" id="editKurvaRealisasiPer" class="form-input" min="0" max="100" step="0.01">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditKurva')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditKurva(r) {
    document.getElementById('editKurvaPeriode').value      = r.periode ? r.periode.substring(0,10) : '';
    document.getElementById('editKurvaRencanaKum').value   = r.rencana_kumulatif;
    document.getElementById('editKurvaRealisasiKum').value = r.realisasi_kumulatif;
    document.getElementById('editKurvaRencanaPer').value   = r.rencana_periode;
    document.getElementById('editKurvaRealisasiPer').value = r.realisasi_periode;
    document.getElementById('formEditKurva').action        = `/projects/{{ $project->id }}/kurvas/${r.id}`;
    openModal('modalEditKurva');
}
</script>
@if($records->isNotEmpty())
<script>
const ctx = document.getElementById('kurvaSChart').getContext('2d');const chartData = @json($chartData);
new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: [
            {
                label: 'Kumulatif Rencana (%)',
                data: chartData.rencana,
                borderColor: '#0075de',
                backgroundColor: 'rgba(0,117,222,.08)',
                fill: true,
                tension: 0.5,
                borderWidth: 2.5,
                pointBackgroundColor: '#0075de',
                pointBorderColor: '#0075de',
                pointRadius: 4,
                pointHoverRadius: 6,
                cubicInterpolationMode: 'default',
            },
            {
                label: 'Kumulatif Aktual (%)',
                data: chartData.realisasi,
                borderColor: '#2a9d99',
                backgroundColor: 'rgba(42,157,153,.08)',
                fill: true,
                tension: 0.5,
                borderWidth: 2.5,
                pointBackgroundColor: '#2a9d99',
                pointBorderColor: '#2a9d99',
                pointRadius: 4,
                pointHoverRadius: 6,
                cubicInterpolationMode: 'default',
            },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Kurva S: Progres Rencana vs Aktual',
                font: { family: 'Inter', size: 15, weight: '700' },
                color: '#000',
                padding: { bottom: 16 },
            },
            legend: {
                position: 'right',
                labels: {
                    font: { family: 'Inter', size: 12 },
                    color: '#31302e',
                    boxWidth: 24,
                    usePointStyle: false,
                }
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toFixed(2)}%`
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Minggu Pelaksanaan',
                    font: { family: 'Inter', size: 12, weight: '600' },
                    color: '#31302e',
                    padding: { top: 8 },
                },
                grid: { color: '#e6e6e6' },
                ticks: { font: { family: 'Inter', size: 11 }, color: '#615d59' }
            },
            y: {
                min: 0,
                max: 120,
                title: {
                    display: true,
                    text: 'Bobot Kumulatif (%)',
                    font: { family: 'Inter', size: 12, weight: '600' },
                    color: '#31302e',
                    padding: { bottom: 8 },
                },
                grid: { color: '#e6e6e6' },
                ticks: {
                    font: { family: 'Inter', size: 11 },
                    color: '#615d59',
                    stepSize: 20,
                    callback: v => v.toFixed(2)
                }
            }
        }
    }
});
</script>
@endif
@endpush
