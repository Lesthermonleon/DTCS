<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — DTCS HIMS</title>
    <link rel="icon" href="{{ asset('assets/images/brand/favicon.svg') }}" type="image/svg+xml" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    {{-- Phosphor Icons --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink:         #0A1F1C;
            --ink-soft:    #13312B;
            --paper:       #F7F5F0;
            --card:        #FFFFFF;
            --line:        #E6E2D6;
            --text:        #132420;
            --text-soft:   #6E7C74;
            --signal:      #14C79A;
            --signal-dark: #0C8F6F;
            --coral:       #E85C55;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* ── ECG trace background ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% 110%, rgba(20,199,154,.07) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Animated ECG dashed path as a decorative SVG layer */
        .ecg-bg {
            position: fixed;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
            opacity: .08;
            pointer-events: none;
            z-index: 0;
        }
        .ecg-bg path {
            stroke-dasharray: 800;
            stroke-dashoffset: 800;
            animation: ecg-draw 3s ease forwards;
        }
        @media (prefers-reduced-motion: reduce) {
            .ecg-bg path { animation: none; stroke-dashoffset: 0; }
        }
        @keyframes ecg-draw {
            to { stroke-dashoffset: 0; }
        }

        /* ── Auth card ── */
        .auth-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 1rem;
        }

        .auth-card {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 1.25rem;
            box-shadow: 0 24px 64px rgba(0,0,0,.45), 0 0 0 1px rgba(20,199,154,.08);
            overflow: hidden;
        }

        /* ── Card header ── */
        .auth-header {
            background: var(--ink-soft);
            padding: 2rem 2rem 1.75rem;
            text-align: center;
            border-bottom: 1px solid rgba(20,199,154,.15);
        }
        .auth-brand-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            margin-bottom: .75rem;
        }
        .auth-pulse-icon {
            width: 38px;
            height: 38px;
        }
        .auth-header h3 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.4rem;
            color: #fff;
            margin: 0;
        }
        .auth-header p {
            font-family: 'IBM Plex Mono', monospace;
            font-size: .65rem;
            font-weight: 500;
            letter-spacing: .08em;
            color: rgba(255,255,255,.4);
            text-transform: uppercase;
            margin: .4rem 0 0;
        }

        /* ── Card body ── */
        .auth-body {
            padding: 2rem;
        }

        /* ── Form controls ── */
        .form-label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: .83rem;
            color: var(--text);
            margin-bottom: .35rem;
        }
        .input-group-text {
            background: var(--card);
            border-color: var(--line);
            color: var(--text-soft);
        }
        .form-control, .form-select {
            font-family: 'Inter', sans-serif;
            font-size: .88rem;
            border-color: var(--line);
            background: var(--card);
            color: var(--text);
            border-radius: .55rem;
            padding: .6rem .9rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .input-group .form-control { border-radius: 0 .55rem .55rem 0; }
        .input-group .input-group-text { border-radius: .55rem 0 0 .55rem; }
        .form-control:focus, .form-select:focus {
            border-color: var(--signal);
            box-shadow: 0 0 0 3px rgba(20,199,154,.18);
            outline: none;
            background: #fff;
        }
        .form-control::placeholder { color: var(--text-soft); }
        .form-check-input:checked {
            background-color: var(--signal);
            border-color: var(--signal);
        }
        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(20,199,154,.18);
        }

        /* ── Submit button ── */
        .btn-auth {
            background: linear-gradient(135deg, var(--signal) 0%, var(--signal-dark) 100%);
            color: var(--ink);
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: .9rem;
            border: none;
            border-radius: .6rem;
            padding: .7rem;
            width: 100%;
            transition: opacity .15s, transform .1s;
        }
        .btn-auth:hover  { opacity: .9; color: var(--ink); }
        .btn-auth:active { transform: scale(.99); }
        .btn-auth:focus-visible {
            outline: 2px solid var(--signal);
            outline-offset: 3px;
        }

        /* ── Links ── */
        a.auth-link {
            color: var(--signal-dark);
            font-size: .82rem;
            text-decoration: none;
        }
        a.auth-link:hover { text-decoration: underline; }

        /* ── Alerts ── */
        .alert {
            position: relative;
            padding: 0.85rem 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #E2E8F0 !important;
            border-radius: 0.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.83rem;
            font-family: 'Inter', sans-serif;
        }
        .alert-success {
            background: #DCFCE7 !important;
            border-left: 4px solid #10B981 !important;
            color: #475569 !important;
        }
        .alert-danger {
            background: #FEE2E2 !important;
            border-left: 4px solid #EF4444 !important;
            color: #475569 !important;
        }
        .alert-warning {
            background: #FEF3C7 !important;
            border-left: 4px solid #F59E0B !important;
            color: #475569 !important;
        }
        .alert-info {
            background: #DBEAFE !important;
            border-left: 4px solid #3B82F6 !important;
            color: #475569 !important;
        }
        .alert-neutral {
            background: #F1F5F9 !important;
            border-left: 4px solid #64748B !important;
            color: #475569 !important;
        }
        .alert .alert-icon {
            align-self: flex-start;
        }
        .alert-success .alert-icon { color: #10B981 !important; }
        .alert-danger .alert-icon { color: #EF4444 !important; }
        .alert-warning .alert-icon { color: #F59E0B !important; }
        .alert-info .alert-icon { color: #3B82F6 !important; }
        .alert-neutral .alert-icon { color: #64748B !important; }
        .invalid-feedback { font-size: .78rem; }

        /* ── Form check label ── */
        .form-check-label { font-size: .82rem; color: var(--text-soft); }
    </style>
</head>
<body>

    {{-- ECG background decoration --}}
    <svg class="ecg-bg" viewBox="0 0 1200 80" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M0,40 L150,40 L180,40 L200,10 L215,70 L230,5 L245,75 L260,40 L290,40 L310,40 L330,40 L400,40 L500,40 L600,40 L700,40 L720,10 L735,70 L750,5 L765,75 L780,40 L900,40 L1200,40"
              fill="none" stroke="#14C79A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>

    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-brand-row">
                    {{-- Pulse icon --}}
                    <svg class="auth-pulse-icon" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect width="38" height="38" rx="9" fill="rgba(20,199,154,0.15)"/>
                        <polyline
                            points="3,19 9,19 12,12 14,26 17,8 19,30 21,14 23,22 26,19 35,19"
                            fill="none"
                            stroke="#14C79A"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                    <h3>DTCS HIMS</h3>
                </div>
                <p>Diagnostic, Treatment &amp; Clinical Services</p>
            </div>
            <div class="auth-body">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Full Screen Cardio Loader Overlay --}}
    <div id="cardio-loader-overlay" style="display: none; position: fixed; inset: 0; background: rgba(10, 31, 28, 0.45); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; flex-direction: column;">
        <l-cardio size="100" stroke="6" speed="2" color="#14C79A"></l-cardio>
        <div style="margin-top: 1rem; font-family: 'Space Grotesk', sans-serif; font-weight: 600; color: #FFFFFF; font-size: 1.1rem; letter-spacing: 0.05em;">Loading...</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Import ldrs cardio loader component --}}
    <script type="module">
        import { cardio } from 'https://cdn.jsdelivr.net/npm/ldrs/+esm';
        cardio.register();
    </script>
    <script>
        document.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            const btn = e.target.querySelector('button[type="submit"], input[type="submit"]');
            if (btn) {
                const overlay = document.getElementById('cardio-loader-overlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                }
                setTimeout(() => {
                    btn.disabled = true;
                }, 10);
            }
        });

        /* ── Global Alert Icons Injector ── */
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                if (!alert.querySelector('.ph-fill, .bi, .alert-icon')) {
                    let iconClass = 'ph-fill ph-info';
                    if (alert.classList.contains('alert-success')) {
                        iconClass = 'ph-fill ph-check-circle';
                    } else if (alert.classList.contains('alert-danger')) {
                        iconClass = 'ph-fill ph-warning-circle';
                    } else if (alert.classList.contains('alert-warning')) {
                        iconClass = 'ph-fill ph-warning';
                    }
                    let icon = document.createElement('i');
                    icon.className = iconClass + ' alert-icon';
                    alert.insertBefore(icon, alert.firstChild);
                }
            });
        });
    </script>
</body>
</html>
