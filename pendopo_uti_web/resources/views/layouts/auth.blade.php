<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — Pendopo Uti</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #B8860B;
            --gold-light: #D4A843;
            --gold-pale: #F5E6C8;
            --cream: #FDF8F0;
            --cream-dark: #F0E6D3;
            --charcoal: #2C2418;
            --brown: #5C4A32;
            --brown-light: #8B7355;
            --rose: #C9A9A6;
            --white: #FFFFFF;
            --error: #B44040;
            --success: #4A7A5B;
            --font-display: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'Jost', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--cream);
            color: var(--charcoal);
            min-height: 100vh;
            display: flex;
        }

        /* ── LEFT PANEL ── */
        .visual-panel {
            flex: 1; position: relative; display: none; overflow: hidden;
        }
        @media (min-width: 1024px) {
            .visual-panel { display: flex; align-items: center; justify-content: center; }
        }
        .visual-panel::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(44,36,24,0.85) 0%, rgba(92,74,50,0.6) 50%, rgba(184,134,11,0.3) 100%);
            z-index: 1;
        }
        .visual-bg {
            position: absolute; inset: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><pattern id="p" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M20 0L40 20L20 40L0 20Z" fill="none" stroke="%23B8860B" stroke-width="0.5" opacity="0.15"/></pattern></defs><rect width="400" height="400" fill="%232C2418"/><rect width="400" height="400" fill="url(%23p)"/></svg>');
            background-size: 200px;
        }
        .visual-content {
            position: relative; z-index: 2; text-align: center; padding: 3rem; max-width: 480px;
        }
        .ornament { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 2rem; }
        .ornament .line { width: 60px; height: 1px; background: var(--gold-light); opacity: 0.6; }
        .ornament .diamond { width: 8px; height: 8px; background: var(--gold-light); transform: rotate(45deg); }
        .visual-content h1 {
            font-family: var(--font-display); font-size: 3.2rem; font-weight: 400;
            color: var(--gold-pale); letter-spacing: 0.04em; line-height: 1.15;
        }
        .visual-content h1 em { font-style: italic; font-weight: 500; color: var(--gold-light); }
        .visual-content .tagline {
            font-size: 0.85rem; font-weight: 300; color: var(--rose);
            letter-spacing: 0.25em; text-transform: uppercase; margin-top: 1rem;
        }
        .visual-content .description {
            font-size: 0.95rem; font-weight: 300; color: rgba(255,255,255,0.6);
            line-height: 1.7; margin-top: 2rem;
        }

        /* ── RIGHT PANEL ── */
        .form-panel {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 2rem; position: relative; min-height: 100vh;
        }
        .form-panel::before {
            content: ''; position: absolute; top: 0; left: 0;
            width: 4px; height: 100%;
            background: linear-gradient(180deg, transparent, var(--gold), var(--gold-light), transparent);
        }
        .form-container { width: 100%; max-width: 440px; }

        /* Brand */
        .brand { text-align: center; margin-bottom: 2.5rem; }
        .brand-icon {
            width: 56px; height: 56px; margin: 0 auto 1rem;
            border: 1.5px solid var(--gold); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--gold-pale) 0%, var(--cream) 100%);
        }
        .brand-icon svg { width: 28px; height: 28px; fill: var(--gold); }
        .brand h2 { font-family: var(--font-display); font-size: 1.8rem; font-weight: 500; color: var(--charcoal); }
        .brand p { font-size: 0.85rem; font-weight: 300; color: var(--brown-light); margin-top: 0.3rem; }

        /* Form */
        .auth-form { display: flex; flex-direction: column; gap: 1.25rem; }
        .form-group { position: relative; }
        .form-group label {
            display: block; font-size: 0.75rem; font-weight: 500; color: var(--brown);
            letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem;
        }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-wrapper .icon {
            position: absolute; left: 14px; width: 18px; height: 18px;
            color: var(--brown-light); pointer-events: none; transition: color 0.3s;
        }
        .input-wrapper input {
            width: 100%; padding: 0.85rem 1rem 0.85rem 2.8rem;
            font-family: var(--font-body); font-size: 0.95rem; font-weight: 400;
            color: var(--charcoal); background: var(--white);
            border: 1.5px solid var(--cream-dark); border-radius: 8px;
            outline: none; transition: all 0.3s ease;
        }
        .input-wrapper input::placeholder { color: var(--brown-light); font-weight: 300; }
        .input-wrapper input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(184,134,11,0.1); }
        .input-wrapper:has(input:focus) .icon { color: var(--gold); }

        .input-wrapper input.is-invalid { border-color: var(--error); }

        .password-toggle {
            position: absolute; right: 14px; background: none; border: none;
            cursor: pointer; color: var(--brown-light); padding: 4px; display: flex; transition: color 0.3s;
        }
        .password-toggle:hover { color: var(--gold); }

        .form-hint { font-size: 0.75rem; color: var(--brown-light); margin-top: 0.35rem; font-weight: 300; }
        .form-error { font-size: 0.75rem; color: var(--error); margin-top: 0.35rem; font-weight: 400; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }

        /* Remember & Forgot */
        .form-options { display: flex; align-items: center; justify-content: space-between; margin-top: -0.25rem; }
        .remember-me { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
        .remember-me input[type="checkbox"] {
            appearance: none; width: 18px; height: 18px; border: 1.5px solid var(--cream-dark);
            border-radius: 4px; background: var(--white); cursor: pointer; transition: all 0.2s; position: relative;
        }
        .remember-me input[type="checkbox"]:checked { background: var(--gold); border-color: var(--gold); }
        .remember-me input[type="checkbox"]:checked::after {
            content: ''; position: absolute; top: 2px; left: 5px; width: 5px; height: 9px;
            border: solid var(--white); border-width: 0 2px 2px 0; transform: rotate(45deg);
        }
        .remember-me span { font-size: 0.85rem; font-weight: 400; color: var(--brown); }
        .forgot-link { font-size: 0.85rem; font-weight: 400; color: var(--gold); text-decoration: none; }
        .forgot-link:hover { color: var(--charcoal); text-decoration: underline; }

        /* Button */
        .btn-primary {
            width: 100%; padding: 0.95rem; font-family: var(--font-body);
            font-size: 0.85rem; font-weight: 500; letter-spacing: 0.15em;
            text-transform: uppercase; color: var(--cream);
            background: linear-gradient(135deg, var(--charcoal) 0%, var(--brown) 100%);
            border: none; border-radius: 8px; cursor: pointer;
            position: relative; overflow: hidden; transition: all 0.4s ease; margin-top: 0.5rem;
        }
        .btn-primary::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            transition: left 0.4s ease;
        }
        .btn-primary:hover::before { left: 0; }
        .btn-primary span { position: relative; z-index: 1; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(44,36,24,0.25); }

        /* Alert */
        .alert {
            padding: 0.85rem 1rem; border-radius: 8px; font-size: 0.85rem;
            display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem;
        }
        .alert-error { background: #FEF2F2; border: 1px solid #FECACA; color: var(--error); }
        .alert-success { background: #F0FDF4; border: 1px solid #BBF7D0; color: var(--success); }

        /* Divider & Footer */
        .divider { display: flex; align-items: center; gap: 1rem; margin: 0.25rem 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--cream-dark); }
        .divider span { font-size: 0.75rem; color: var(--brown-light); font-weight: 300; }
        .form-footer { text-align: center; margin-top: 0.5rem; }
        .form-footer p { font-size: 0.875rem; color: var(--brown-light); font-weight: 300; }
        .form-footer a { color: var(--gold); text-decoration: none; font-weight: 500; }
        .form-footer a:hover { color: var(--charcoal); text-decoration: underline; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-container > * { animation: fadeUp 0.6s ease-out both; }
        .form-container > *:nth-child(1) { animation-delay: 0.1s; }
        .form-container > *:nth-child(2) { animation-delay: 0.2s; }
        .form-container > *:nth-child(3) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <!-- LEFT: Visual -->
    <div class="visual-panel">
        <div class="visual-bg"></div>
        <div class="visual-content">
            <div class="ornament">
                <span class="line"></span><span class="diamond"></span><span class="line"></span>
            </div>
            <h1>Pendopo <em>Uti</em></h1>
            <p class="tagline">Wedding Venue Booking</p>
            <p class="description">@yield('visual-description')</p>
        </div>
    </div>

    <!-- RIGHT: Form -->
    <div class="form-panel">
        <div class="form-container">
            @yield('content')
        </div>
    </div>

    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.innerHTML = isPassword
                ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
                : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        }
    </script>
</body>
</html>