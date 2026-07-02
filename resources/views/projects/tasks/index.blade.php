@extends('layouts.app')
@section('title', 'Task — '.$project->nama)
@section('topbar-title', 'Task')

@section('content')
@php
$myUserId      = auth()->id();
$myMembership  = $members->firstWhere('user_id', $myUserId);
$myRole        = $myMembership?->peran ?? ($project->created_by === $myUserId ? 'Owner' : null);
$isPM          = in_array($myRole, ['Owner', 'Project_Manager']);
// My tasks = tasks assigned to me, or all if PM
$myTasks       = $isPM ? $tasks : $tasks->filter(fn($t) => $t->assigned_to === $myUserId)->values();
@endphp

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between">
    <div>
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> {{ $project->nama }}</a>
        <h1 class="page-title" style="margin-top:4px">Task Management</h1>
        @if(!$isPM)
        <div style="margin-top:6px;display:inline-flex;align-items:center;gap:6px;
             background:#dbeafe;color:#1d4ed8;padding:4px 10px;border-radius:var(--r-full);font-size:12px">
            <i class="fas fa-eye"></i> Menampilkan task yang ditugaskan ke Anda
        </div>
        @endif
    </div>
    @if($isPM)
    <button class="btn btn-primary" onclick="openModal('modalAddTask')"><i class="fas fa-plus"></i> Tambah Task</button>
    @endif
</div>

<!-- Filter -->
<div style="display:flex;gap:var(--sp-sm);margin-bottom:var(--sp-md);flex-wrap:wrap">
    @foreach([''=>'Semua','Todo'=>'Todo','In_Progress'=>'In Progress','Blocked'=>'Blocked','Done'=>'Done'] as $val=>$label)
    <a href="{{ route('projects.tasks.index', [$project, 'status'=>$val]) }}"
       class="btn btn-utility {{ request('status',$val===''?'':null)===$val ? '' : '' }}"
       style="{{ request('status')===$val||($val===''&&!request('status')) ? 'background:var(--primary);color:#fff;border-color:var(--primary)' : '' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="card">
    @if($myTasks->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-check-square"></i></div>
        <p>{{ $isPM ? 'Belum ada task untuk proyek ini.' : 'Tidak ada task yang ditugaskan ke Anda.' }}</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th>Task</th><th>Fase</th><th>Assignee</th><th>Prioritas</th><th>Status</th><th>Progress</th><th>Mulai</th><th>Tenggat</th><th></th></tr></thead>
            <tbody>
            @foreach($myTasks as $t)
            <tr>
                <td>
                    <div style="font-weight:500">{{ $t->nama }}</div>
                    @if($t->parent_task_id)<div style="font-size:11px;color:var(--ink-faint)">Sub-task</div>@endif
                    @if($t->deskripsi)<div style="font-size:12px;color:var(--ink-faint)">{{ Str::limit($t->deskripsi,50) }}</div>@endif
                </td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $t->phase?->nama ?? '—' }}</td>
                <td style="font-size:13px">
                    @if($t->assignee)
                    <div style="display:flex;align-items:center;gap:6px">
                        <div style="width:22px;height:22px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0">
                            {{ strtoupper(substr($t->assignee->name,0,1)) }}
                        </div>
                        <span style="color:var(--ink-secondary)">{{ $t->assignee->name }}</span>
                    </div>
                    @else
                    <span style="color:var(--ink-faint)">—</span>
                    @endif
                </td>
                <td>
                    @php $pm=['Low'=>'badge-grey','Medium'=>'badge-blue','High'=>'badge-orange','Critical'=>'badge-red']; @endphp
                    <span class="badge {{ $pm[$t->prioritas]??'badge-grey' }}">{{ $t->prioritas }}</span>
                </td>
                <td>
                    @php $sm=['Todo'=>'badge-grey','In_Progress'=>'badge-blue','Blocked'=>'badge-red','Done'=>'badge-green']; @endphp
                    <span class="badge {{ $sm[$t->status]??'badge-grey' }}">{{ $t->status }}</span>
                </td>
                <td style="min-width:100px">
                    <div style="display:flex;align-items:center;gap:6px">
                        <div class="progress-bar" style="flex:1">
                            <div class="progress-fill {{ $t->persen_selesai>=100?'success':($t->persen_selesai>=50?'':'warning') }}" style="width:{{ $t->persen_selesai }}%"></div>
                        </div>
                        <span style="font-size:12px;color:var(--ink-faint);white-space:nowrap">{{ $t->persen_selesai }}%</span>
                    </div>
                </td>
                <td style="font-size:13px;color:var(--ink-muted)">{{ $t->tanggal_mulai?->format('d M') ?? '—' }}</td>
                <td style="font-size:13px;color:var(--ink-muted)">
                    {{ $t->tanggal_selesai?->format('d M') ?? '—' }}
                    @if($t->durasi_hari)
                    <span style="font-size:11px;color:var(--ink-faint)">({{ $t->durasi_hari }}h)</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:4px">
                        {{-- Progress update: semua bisa --}}
                        <button class="btn btn-icon" onclick='openEditTask({{ $t->toJson() }}, {{ $isPM ? "true" : "false" }})' title="{{ $isPM ? 'Edit' : 'Update Progress' }}">
                            <i class="fas fa-{{ $isPM ? 'pencil' : 'arrow-up-right-dots' }}"></i>
                        </button>
                        @if($isPM)
                        <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $t]) }}" onsubmit="return confirm('Hapus task ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon" style="color:#ef4444"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Modal: Add Task -->
<div class="modal-backdrop" id="modalAddTask">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">Tambah Task</span>
            <button class="modal-close" onclick="closeModal('modalAddTask')">×</button>
        </div>
        <form method="POST" action="{{ route('projects.tasks.store', $project) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Task <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" class="form-input" placeholder="Contoh: Setup infrastruktur server" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Fase</label>
                    <select name="phase_id" class="form-select">
                        <option value="">— Pilih Fase —</option>
                        @foreach($phases as $ph)<option value="{{ $ph->id }}">{{ $ph->nama }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-select">
                        <option>Low</option><option selected>Medium</option><option>High</option><option>Critical</option>
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
            {{-- durasi_hari dihitung otomatis dari selisih tanggal --}}
            <div class="form-group">
                <label class="form-label">Penanggung Jawab</label>
                <select name="assigned_to" class="form-select">
                    <option value="">— Tidak ditugaskan —</option>
                    @foreach($members as $m)<option value="{{ $m->user->id }}">{{ $m->user->name }}</option>@endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Sub-task dari</label>
                <select name="parent_task_id" class="form-select">
                    <option value="">— Task mandiri —</option>
                    @foreach($tasks as $pt)<option value="{{ $pt->id }}">{{ $pt->nama }}</option>@endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea" placeholder="Detail task..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddTask')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Task -->
<div class="modal-backdrop" id="modalEditTask">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">Edit Task</span>
            <button class="modal-close" onclick="closeModal('modalEditTask')">×</button>
        </div>
        <form method="POST" id="formEditTask">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Task <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" id="editTaskName" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Fase</label>
                    <select name="phase_id" id="editTaskPhase" class="form-select">
                        <option value="">— Pilih Fase —</option>
                        @foreach($phases as $ph)<option value="{{ $ph->id }}">{{ $ph->nama }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" id="editTaskPrioritas" class="form-select">
                        <option>Low</option><option>Medium</option><option>High</option><option>Critical</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editTaskStatus" class="form-select">
                        <option>Todo</option><option>In_Progress</option><option>Blocked</option><option>Done</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Progress (%)</label>
                    <input type="number" name="persen_selesai" id="editTaskPersen" class="form-input" min="0" max="100">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="editTaskStart" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="editTaskEnd" class="form-input">
                </div>
            </div>
            {{-- durasi_hari dihitung otomatis --}}
            <div class="form-group">
                <label class="form-label">Penanggung Jawab</label>
                <select name="assigned_to" id="editTaskAssignee" class="form-select">
                    <option value="">— Tidak ditugaskan —</option>
                    @foreach($members as $m)<option value="{{ $m->user->id }}">{{ $m->user->name }}</option>@endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="editTaskDesc" class="form-textarea"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditTask')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditTask(t, isPM) {
    document.getElementById('editTaskName').value      = t.nama;
    document.getElementById('editTaskPhase').value     = t.phase_id ?? '';
    document.getElementById('editTaskPrioritas').value = t.prioritas;
    document.getElementById('editTaskStatus').value    = t.status;
    document.getElementById('editTaskPersen').value    = t.persen_selesai;
    document.getElementById('editTaskStart').value     = t.tanggal_mulai   ? t.tanggal_mulai.substring(0,10)   : '';
    document.getElementById('editTaskEnd').value       = t.tanggal_selesai ? t.tanggal_selesai.substring(0,10) : '';
    document.getElementById('editTaskDurasi').value    = t.durasi_hari ?? '';
    document.getElementById('editTaskAssignee').value  = t.assigned_to ?? '';
    document.getElementById('editTaskDesc').value      = t.deskripsi ?? '';
    document.getElementById('formEditTask').action     = `/projects/{{ $project->id }}/tasks/${t.id}`;

    // PM-only fields: disable jika bukan PM
    const pmFields = ['editTaskName','editTaskPhase','editTaskPrioritas','editTaskStart',
                      'editTaskEnd','editTaskDurasi','editTaskAssignee','editTaskDesc'];
    pmFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.disabled = !isPM;
    });

    // Update modal title
    document.querySelector('#modalEditTask .modal-title').textContent =
        isPM ? 'Edit Task' : 'Update Progress & Status';

    openModal('modalEditTask');
}
</script>
@endpush
