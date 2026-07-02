@extends('layouts.app')
@section('title', 'Proyek Baru')
@section('topbar-title', 'Buat Proyek')

@section('content')
<div style="max-width:640px">
    <div class="page-header">
        <a href="{{ route('projects.index') }}" style="font-size:13px;color:var(--ink-muted);text-decoration:none"><i class="fas fa-arrow-left"></i> Kembali</a>
        <h1 class="page-title" style="margin-top:var(--sp-sm)">Proyek Baru</h1>
    </div>

    <div class="card card-elevated">
        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:16px">
            <i class="fas fa-circle-xmark"></i>
            <div>
                <strong>Form tidak dapat disimpan:</strong>
                <ul style="margin:4px 0 0 16px;font-size:13px">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Proyek <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" class="form-input" value="{{ old('nama') }}" placeholder="Contoh: Pengembangan Sistem SCADA" required>
                @error('nama')<span style="font-size:12px;color:#ef4444">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea" placeholder="Gambaran umum proyek...">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-input" value="{{ old('tanggal_mulai') }}" required>
                    @error('tanggal_mulai')<span style="font-size:12px;color:#ef4444">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-input" value="{{ old('tanggal_selesai') }}" required>
                    @error('tanggal_selesai')<span style="font-size:12px;color:#ef4444">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Anggaran (Rp) <span style="color:#ef4444">*</span></label>
                    <input type="text" inputmode="numeric" name="anggaran" class="form-input"
                           value="{{ old('anggaran') ? number_format(old('anggaran'),0,',','.') : '' }}"
                           placeholder="Contoh: 1.500.000.000" required autocomplete="off">
                    @error('anggaran')<span style="font-size:12px;color:#ef4444">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['Draft','Active','Suspended','Completed','Cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status','Draft') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="padding:0;margin:0;border:none;margin-top:var(--sp-lg)">
                <a href="{{ route('projects.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Proyek</button>
            </div>
        </form>
    </div>
</div>
@endsection
