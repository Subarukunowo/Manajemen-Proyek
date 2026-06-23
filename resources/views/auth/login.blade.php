@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<div class="auth-card">

    <!-- Decorative sticker dots -->
    <div class="auth-dots">
        <div class="dot" style="background:#0075de"></div>
        <div class="dot" style="background:#d6b6f6"></div>
        <div class="dot" style="background:#ff64c8"></div>
        <div class="dot" style="background:#2a9d99"></div>
        <div class="dot" style="background:#1aae39"></div>
    </div>

    <h1 class="auth-title">Selamat datang kembali</h1>
    <p class="auth-sub">Masuk ke akun untuk melanjutkan.</p>

    @if($errors->any())
    <div class="alert-error">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus
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
                placeholder="••••••••"
                autocomplete="current-password"
                required
            >
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="remember-row">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Ingat saya</label>
        </div>

        <button type="submit" class="btn-submit">Masuk</button>
    </form>

    <div class="auth-divider">atau</div>

    <div class="auth-link">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>
</div>
@endsection
