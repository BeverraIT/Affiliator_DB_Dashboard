<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Database Affiliator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --md-primary:            #1565C0;
            --md-on-primary:         #FFFFFF;
            --md-primary-container:  #D6E4FF;
            --md-on-primary-cont:    #001A41;
            --md-secondary:          #1B5E20;
            --md-secondary-cont:     #C8E6C9;
            --md-tertiary:           #6A1B9A;
            --md-tertiary-cont:      #EDE7F6;
            --md-surface:            #FDFBFF;
            --md-on-surface:         #1A1B1F;
            --md-on-surface-var:     #44464F;
            --md-outline:            #74777F;
            --md-outline-var:        #C4C6D0;
            --md-error:              #B3261E;
            --md-on-error:           #FFFFFF;
            --md-error-cont:         #FFDAD6;
            --md-on-error-cont:      #410E0B;
            --md-surface-cont:       #F0F0F7;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: var(--md-surface);
        }

        /* ── PAGE LAYOUT ── */
        .login-page {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* ─────────────────────────────────────────
           LEFT PANEL — Hero / Brand
        ───────────────────────────────────────── */
        .hero-panel {
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, #0D47A1 0%, #1565C0 35%, #283593 65%, #4A148C 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 44px;
            color: #fff;
        }

        /* Animated blob shapes */
        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: 0.12;
            animation: floatBlob 8s ease-in-out infinite;
            pointer-events: none;
        }
        .blob-1 { width: 320px; height: 320px; background: #90CAF9; top: -80px; right: -60px; animation-delay: 0s; }
        .blob-2 { width: 200px; height: 200px; background: #CE93D8; bottom: 120px; left: -60px; animation-delay: -3s; }
        .blob-3 { width: 140px; height: 140px; background: #A5D6A7; bottom: -40px; right: 80px; animation-delay: -5s; }
        .blob-4 { width: 100px; height: 100px; background: #FFF176; top: 200px; left: 30px; animation-delay: -2s; }

        @keyframes floatBlob {
            0%, 100% { transform: translateY(0px) scale(1); }
            50%       { transform: translateY(-24px) scale(1.04); }
        }

        /* Dot grid decoration */
        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.15) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        /* Brand area */
        .hero-brand {
            position: relative;
            z-index: 1;
        }
        .brand-icon-wrap {
            width: 56px; height: 56px;
            background: rgba(255,255,255,0.18);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.25);
        }
        .brand-icon-wrap .material-symbols-rounded { font-size: 28px; color: #fff; }
        .hero-title {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }
        .hero-subtitle {
            font-size: 0.9rem;
            font-weight: 400;
            color: rgba(255,255,255,0.72);
            line-height: 1.55;
            max-width: 280px;
        }

        /* Feature cards */
        .feature-grid {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .feature-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 16px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            opacity: 0;
            transform: translateX(-24px);
            animation: slideInCard 0.6s ease forwards;
        }
        .feature-card:nth-child(1) { animation-delay: 0.15s; }
        .feature-card:nth-child(2) { animation-delay: 0.30s; }
        .feature-card:nth-child(3) { animation-delay: 0.45s; }

        @keyframes slideInCard {
            to { opacity: 1; transform: translateX(0); }
        }

        .fc-icon {
            width: 40px; height: 40px; flex-shrink: 0;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
        }
        .fc-icon.blue   { background: rgba(144,202,249,0.25); }
        .fc-icon.green  { background: rgba(165,214,167,0.25); }
        .fc-icon.purple { background: rgba(206,147,216,0.25); }
        .fc-icon .material-symbols-rounded { font-size: 20px; color: #fff; }

        .fc-text {}
        .fc-label { font-size: 0.8rem; font-weight: 600; color: #fff; margin-bottom: 2px; }
        .fc-desc  { font-size: 0.72rem; color: rgba(255,255,255,0.65); }

        /* ─────────────────────────────────────────
           RIGHT PANEL — Login Form
        ───────────────────────────────────────── */
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
            background: var(--md-surface);
        }

        .form-inner {
            width: 100%;
            max-width: 360px;
        }

        .form-header {
            margin-bottom: 32px;
        }
        .form-eyebrow {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--md-primary);
            margin-bottom: 8px;
        }
        .form-headline {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--md-on-surface);
            line-height: 1.2;
            margin-bottom: 6px;
        }
        .form-support {
            font-size: 0.85rem;
            color: var(--md-on-surface-var);
        }

        /* MD3 Error banner */
        .md-error-banner {
            background: var(--md-error-cont);
            border: 1px solid rgba(179,38,30,0.25);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .md-error-banner .material-symbols-rounded { font-size: 18px; color: var(--md-error); flex-shrink: 0; margin-top: 1px; }
        .md-error-banner ul { list-style: none; }
        .md-error-banner li { font-size: 0.8rem; color: var(--md-on-error-cont); font-weight: 500; }

        /* MD3 Outlined Text Field */
        .md-field {
            position: relative;
            margin-bottom: 20px;
        }
        .md-field__input {
            width: 100%;
            height: 56px;
            padding: 20px 16px 8px;
            font-family: inherit;
            font-size: 0.9375rem;
            color: var(--md-on-surface);
            background: transparent;
            border: 1.5px solid var(--md-outline-var);
            border-radius: 4px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            caret-color: var(--md-primary);
        }
        .md-field__input:focus {
            border-color: var(--md-primary);
            border-width: 2px;
            box-shadow: inset 0 0 0 0 transparent;
        }
        .md-field__input.has-error {
            border-color: var(--md-error);
        }
        .md-field__input.has-error:focus {
            border-color: var(--md-error);
        }

        /* Floating label */
        .md-field__label {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.9375rem;
            color: var(--md-outline);
            background: var(--md-surface);
            padding: 0 4px;
            pointer-events: none;
            transition: top 0.18s ease, font-size 0.18s ease, color 0.18s ease;
        }
        .md-field__input:focus + .md-field__label,
        .md-field__input:not(:placeholder-shown) + .md-field__label {
            top: 0px;
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--md-primary);
        }
        .md-field__input.has-error + .md-field__label,
        .md-field__input.has-error:focus + .md-field__label {
            color: var(--md-error);
        }

        /* Trailing icon (password toggle) */
        .md-field__trailing {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 50%;
            color: var(--md-on-surface-var);
            display: flex; align-items: center;
            transition: background 0.15s, color 0.15s;
        }
        .md-field__trailing:hover { background: rgba(68,70,79,0.08); color: var(--md-on-surface); }
        .md-field__trailing .material-symbols-rounded { font-size: 20px; }

        /* MD3 Filled Button */
        .btn-md3-filled {
            width: 100%;
            height: 40px;
            background: var(--md-primary);
            color: var(--md-on-primary);
            border: none;
            border-radius: 20px;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.15s;
            margin-top: 8px;
        }
        .btn-md3-filled::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--md-on-primary);
            opacity: 0;
            transition: opacity 0.15s;
        }
        .btn-md3-filled:hover { box-shadow: 0 2px 8px rgba(21,101,192,0.35); }
        .btn-md3-filled:hover::before  { opacity: 0.08; }
        .btn-md3-filled:active { transform: scale(0.98); }
        .btn-md3-filled:active::before { opacity: 0.12; }
        .btn-md3-filled .material-symbols-rounded { font-size: 18px; }

        /* Loading spinner inside button */
        .btn-spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-md3-filled.loading .btn-label,
        .btn-md3-filled.loading .material-symbols-rounded { display: none; }
        .btn-md3-filled.loading .btn-spinner { display: block; }

        /* Remember me */
        .md-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0 4px;
        }
        .md-remember input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--md-primary);
            cursor: pointer;
        }
        .md-remember label {
            font-size: 0.8125rem;
            color: var(--md-on-surface-var);
            cursor: pointer;
            user-select: none;
        }

        /* Form footer */
        .form-footer {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--md-outline-var);
            text-align: center;
        }
        .form-footer p {
            font-size: 0.75rem;
            color: var(--md-on-surface-var);
        }
        .form-footer strong { color: var(--md-on-surface); }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .login-page { grid-template-columns: 1fr; }
            .hero-panel  { display: none; }
            .form-panel  { padding: 32px 24px; min-height: 100vh; }
        }
    </style>
</head>
<body>
<div class="login-page">

    <!-- ══════════════════════════════════════
         LEFT PANEL — Hero
    ══════════════════════════════════════ -->
    <div class="hero-panel">
        <!-- Decorative blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>

        <!-- Brand -->
        <div class="hero-brand">
            <div class="brand-icon-wrap">
                <span class="material-symbols-rounded">hub</span>
            </div>
            <div class="hero-title">Database<br>Affiliator</div>
            <div class="hero-subtitle">Platform manajemen terpadu untuk kreator, sampel produk, dan follow up konten afiliasi TikTok.</div>
        </div>

        <!-- Feature highlights -->
        <div class="feature-grid">
            <div class="feature-card">
                <div class="fc-icon blue">
                    <span class="material-symbols-rounded">group</span>
                </div>
                <div class="fc-text">
                    <div class="fc-label">Manajemen Kreator</div>
                    <div class="fc-desc">Kelola data kreator & PIC secara terpusat</div>
                </div>
            </div>
            <div class="feature-card">
                <div class="fc-icon green">
                    <span class="material-symbols-rounded">inventory_2</span>
                </div>
                <div class="fc-text">
                    <div class="fc-label">Kirim Sampel</div>
                    <div class="fc-desc">Lacak pengiriman & status sampel produk</div>
                </div>
            </div>
            <div class="feature-card">
                <div class="fc-icon purple">
                    <span class="material-symbols-rounded">trending_up</span>
                </div>
                <div class="fc-text">
                    <div class="fc-label">Follow Up Konten</div>
                    <div class="fc-desc">Pantau progres konten & GMV affiliasi</div>
                </div>
            </div>
        </div>

    </div>

    <!-- ══════════════════════════════════════
         RIGHT PANEL — Form
    ══════════════════════════════════════ -->
    <div class="form-panel">
        <div class="form-inner">
            <div class="form-header">
                <div class="form-eyebrow">Selamat datang</div>
                <div class="form-headline">Masuk ke akun Anda</div>
                <div class="form-support">Gunakan email dan password yang terdaftar.</div>
            </div>

            @if ($errors->any())
                <div class="md-error-banner">
                    <span class="material-symbols-rounded">error</span>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST" id="loginForm">
                @csrf

                <!-- Email field -->
                <div class="md-field">
                    <input
                        type="email"
                        class="md-field__input @error('email') has-error @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder=" "
                        required
                        autofocus
                        autocomplete="email"
                    >
                    <label class="md-field__label" for="email">Alamat Email</label>
                </div>

                <!-- Password field -->
                <div class="md-field">
                    <input
                        type="password"
                        class="md-field__input @error('password') has-error @enderror"
                        id="password"
                        name="password"
                        placeholder=" "
                        required
                        autocomplete="current-password"
                        style="padding-right: 48px;"
                    >
                    <label class="md-field__label" for="password">Password</label>
                    <button type="button" class="md-field__trailing" id="togglePassword" title="Tampilkan password">
                        <span class="material-symbols-rounded" id="toggleIcon">visibility</span>
                    </button>
                </div>

                <!-- Remember me -->
                <div class="md-remember">
                    <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn-md3-filled" id="submitBtn">
                    <span class="material-symbols-rounded">login</span>
                    <span class="btn-label">Masuk</span>
                    <div class="btn-spinner"></div>
                </button>
            </form>

            <div class="form-footer">
                <p>Database Affiliator &mdash; <strong>Internal Tool</strong></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle
    const toggle = document.getElementById('togglePassword');
    const pwInput = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');

    toggle.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type = isHidden ? 'text' : 'password';
        icon.textContent = isHidden ? 'visibility_off' : 'visibility';
        toggle.title = isHidden ? 'Sembunyikan password' : 'Tampilkan password';
    });

    // Loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        document.getElementById('submitBtn').classList.add('loading');
    });
</script>
</body>
</html>
