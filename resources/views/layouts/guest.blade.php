<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — Manajemen Proyek</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       #0075de;
            --primary-active:#005bab;
            --secondary:     #213183;
            --canvas:        #ffffff;
            --canvas-soft:   #f6f5f4;
            --surface:       #ffffff;
            --ink:           #000000;
            --ink-secondary: #31302e;
            --ink-muted:     #615d59;
            --ink-faint:     #a39e98;
            --hairline:      #e6e6e6;
            --r-xs: 4px; --r-md: 8px; --r-lg: 12px; --r-xl: 16px; --r-full: 9999px;
            --shadow-2: 0 2px 8px rgba(0,0,0,.04),0 8px 24px rgba(0,0,0,.06),0 23px 52px rgba(0,0,0,.05);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
            background: var(--canvas-soft);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Hero band top */
        .auth-hero {
            background: var(--secondary);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .auth-hero .brand {
            font-size: 16px; font-weight: 700;
            color: #fff; letter-spacing: -.25px;
        }
        .auth-hero .brand span { opacity: .55; font-weight: 400; }
        /* Center card */
        .auth-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }
        .auth-card {
            background: var(--surface);
            border-radius: var(--r-xl);
            box-shadow: var(--shadow-2);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        .auth-title {
            font-size: 22px; font-weight: 700;
            letter-spacing: -.25px; color: var(--ink);
            margin-bottom: 6px;
        }
        .auth-sub {
            font-size: 14px; color: var(--ink-muted);
            margin-bottom: 28px;
        }
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 500;
            color: var(--ink-secondary); margin-bottom: 6px;
        }
        .form-input {
            width: 100%; padding: 9px 11px; font-family: inherit;
            font-size: 14px; color: var(--ink);
            background: var(--surface); border: 1px solid #ddd;
            border-radius: var(--r-xs); outline: none;
            transition: border-color .12s, box-shadow .12s;
        }
        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,117,222,.12);
        }
        .form-input.is-error { border-color: #ef4444; }
        .field-error { font-size: 12px; color: #ef4444; margin-top: 4px; display: block; }
        .btn-submit {
            width: 100%; padding: 10px;
            background: var(--primary); color: #fff;
            border: none; border-radius: var(--r-full);
            font-family: inherit; font-size: 15px; font-weight: 500;
            cursor: pointer; margin-top: 8px;
            transition: background .12s, transform .08s;
        }
        .btn-submit:hover  { background: var(--primary-active); }
        .btn-submit:active { transform: scale(.98); }
        .auth-divider {
            text-align: center; font-size: 13px;
            color: var(--ink-faint); margin: 20px 0;
            position: relative;
        }
        .auth-divider::before, .auth-divider::after {
            content: ''; position: absolute; top: 50%;
            width: 40%; height: 1px; background: var(--hairline);
        }
        .auth-divider::before { left: 0; }
        .auth-divider::after  { right: 0; }
        .auth-link {
            text-align: center; font-size: 14px; color: var(--ink-muted);
            margin-top: 20px;
        }
        .auth-link a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .auth-link a:hover { text-decoration: underline; }
        .alert-error {
            background: #fee2e2; color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: var(--r-md); padding: 10px 14px;
            font-size: 13px; margin-bottom: 16px;
        }
        .remember-row {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: var(--ink-muted); margin-top: 4px;
        }
        /* Decorative dots (sticker palette) */
        .auth-dots {
            display: flex; gap: 6px; margin-bottom: 24px;
        }
        .dot {
            width: 8px; height: 8px; border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="auth-hero">
        <img src="{{ asset('favicon.svg') }}" width="28" height="28" alt="Logo" style="flex-shrink:0">
        <div class="brand">Manajemen Proyek <span>— Internal Dashboard</span></div>
    </div>
    <div class="auth-wrap">
        @yield('content')
    </div>
</body>
</html>
