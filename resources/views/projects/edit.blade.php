@extends('layouts.app')
@section('title', 'Edit Proyek')
@section('topbar-title', 'Edit Proyek')

@section('content')
<div style="max-width:640px">
    <div class="page-header">
        <a href="{{ route('projects.show', $project) }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> Kembali</a>
        <h1 class="page-title" style="margin-top:var(--sp-sm)">Edit: {{ $project->nama }}</h1>
        <p class="page-desc">{{ $project->kode }}</p>
    </div>

    <div class="card card-elevated">
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Proyek <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" class="form-input" value="{{ old('nama', $project->nama) }}" required>
                @error('nama')<span style="font-size:12px;color:#ef4444">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea">{{ old('deskripsi', $project->deskripsi) }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-input" value="{{ old('tanggal_mulai', $project->tanggal_mulai?->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-input" value="{{ old('tanggal_selesai', $project->tanggal_selesai?->format('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Anggaran (Rp) <span style="color:#ef4444">*</span></label>
                    <input type="number" name="anggaran" class="form-input" value="{{ old('anggaran', $project->anggaran) }}" min="0" step="1000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['Draft','Active','Suspended','Completed','Cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status',$project->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:var(--sp-sm);margin-top:var(--sp-lg);padding-top:var(--sp-md);border-top:1px solid var(--hairline)">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
