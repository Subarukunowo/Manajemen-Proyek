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

    <a href="{{ route('google.redirect') }}"
       style="display:flex;align-items:center;justify-content:center;gap:10px;
              width:100%;padding:12px;border:1px solid var(--hairline);border-radius:var(--r-full);
              background:var(--surface);color:var(--ink-secondary);font-size:14px;font-weight:500;
              text-decoration:none;transition:background .12s;margin-bottom:8px"
       onmouseover="this.style.background='var(--canvas-soft)'"
       onmouseout="this.style.background='var(--surface)'">
        <svg width="18" height="18" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Masuk dengan Google
    </a>

    {{-- ═══ Form manual: hanya untuk emergency admin, diakses via /login?emergency=1 ═══ --}}
    @if(request('emergency') === '1')
    <div class="auth-divider">akses darurat</div>

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

        <button type="submit" class="btn-submit">Masuk (Darurat)</button>
    </form>
    @endif

</div>
@endsection