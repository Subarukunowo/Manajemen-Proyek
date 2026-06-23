@extends('layouts.guest')
@section('title', 'Daftar')

@section('content')
<div class="auth-card">

    <!-- Decorative sticker dots -->
    <div class="auth-dots">
        <div class="dot" style="background:#dd5b00"></div>
        <div class="dot" style="background:#0075de"></div>
        <div class="dot" style="background:#2a9d99"></div>
        <div class="dot" style="background:#d6b6f6"></div>
        <div class="dot" style="background:#1aae39"></div>
    </div>

    <h1 class="auth-title">Buat akun baru</h1>
    <p class="auth-sub">Daftarkan diri untuk mengakses dashboard.</p>

    @if($errors->any())
    <div class="alert-error">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Nama Lengkap</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                value="{{ old('name') }}"
                placeholder="Nama Anda"
                autocomplete="name"
                autofocus
                required
            >
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                value="{{ old('email') }}"
                placeholder="nama@perusahaan.com"
                autocomplete="email"
                required
            >
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                placeholder="Minimal 8 karakter"
                autocomplete="new-password"
                required
            >
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-input"
                placeholder="Ulangi password"
                autocomplete="new-password"
                required
            >
        </div>

        <button type="submit" class="btn-submit">Buat Akun</button>
    </form>

    <div class="auth-link">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>
</div>
@endsection
