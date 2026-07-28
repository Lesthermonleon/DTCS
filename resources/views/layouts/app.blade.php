<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HIMS DTCS</title>

    <link rel="icon" href="{{ asset('assets/images/brand/favicon.svg') }}" type="image/svg+xml" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    {{-- Phosphor Icons --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css" />
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Skeleton Loading System --}}
    <link rel="stylesheet" href="{{ asset('css/skeleton.css') }}">
    {{-- Google Fonts: Space Grotesk + Inter + IBM Plex Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    {{-- Early Theme Initialization Script --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem('hims_theme') || 'system';
            let theme = savedTheme;
            if (savedTheme === 'system') {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <style>
        /* ══════════════════════════════════════
           DESIGN TOKENS
        ══════════════════════════════════════ */
        :root {
            --ink:          #0A1F1C;
            --ink-soft:     #13312B;
            --paper:        #F7F5F0;
            --card:         #FFFFFF;
            --line:         #E6E2D6;
            --text:         #132420;
            --text-soft:    #6E7C74;
            --signal:       #14C79A;
            --signal-dark:  #0C8F6F;
            --coral:        #E85C55;
            --amber:        #E0A030;
            --steel:        #4C7EA8;

            --sidebar-width: 260px;
            --topbar-height: 60px;
            --font-display: 'Space Grotesk', sans-serif;
            --font-body:    'Inter', sans-serif;
            --font-mono:    'IBM Plex Mono', monospace;
        }

        /* Dark Theme Overrides */
        html[data-theme="dark"] {
            --paper:        #0B1412;
            --card:         #12221E;
            --line:         #1E3630;
            --text:         #E2E8F0;
            --text-soft:    #94A3B8;
        }
        html[data-theme="dark"] body {
            background-color: var(--paper);
            color: var(--text);
        }
        html[data-theme="dark"] #topbar {
            background: var(--card);
            border-bottom-color: var(--line);
        }
        html[data-theme="dark"] #page-titlebar {
            background: var(--paper);
            border-bottom-color: var(--line);
        }
        html[data-theme="dark"] #page-titlebar-text {
            color: #F8FAFC;
        }
        html[data-theme="dark"] .table th {
            background: #172B26;
            color: #94A3B8;
            border-bottom-color: var(--line);
        }
        html[data-theme="dark"] .table td {
            color: #E2E8F0;
            border-bottom-color: var(--line);
        }
        html[data-theme="dark"] .topbar-search input {
            background: var(--paper);
            border-color: var(--line);
            color: var(--text);
        }
        html[data-theme="dark"] .card {
            background: var(--card);
            border-color: var(--line);
        }
        html[data-theme="dark"] .card-header {
            border-bottom-color: var(--line);
            color: #F8FAFC;
        }
        html[data-theme="dark"] .stat-card {
            background: var(--card);
            border-color: var(--line);
        }
        html[data-theme="dark"] .stat-value {
            color: #F8FAFC;
        }
        html[data-theme="dark"] .topbar-user {
            border-color: var(--line);
        }
        html[data-theme="dark"] .topbar-user-name {
            color: #F8FAFC;
        }
        html[data-theme="dark"] .topbar-notif {
            border-color: var(--line);
        }
        html[data-theme="dark"] .topbar-clock-date {
            color: #E2E8F0;
        }

        /* ── Dark Mode Contrast and Element Readability Overrides ── */
        html[data-theme="dark"] .text-dark {
            color: #f8fafc !important;
        }
        html[data-theme="dark"] .text-secondary {
            color: #94a3b8 !important;
        }
        html[data-theme="dark"] .text-soft {
            color: #94a3b8 !important;
        }
        html[data-theme="dark"] .bg-light {
            background-color: #172d28 !important;
            color: #e2e8f0 !important;
        }
        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select {
            background-color: #0b1412 !important;
            border-color: #1e3630 !important;
            color: #e2e8f0 !important;
        }
        html[data-theme="dark"] .form-control:focus,
        html[data-theme="dark"] .form-select:focus {
            background-color: #12221e !important;
            border-color: var(--signal) !important;
            box-shadow: 0 0 0 0.25rem rgba(20, 199, 154, 0.25) !important;
            color: #e2e8f0 !important;
        }
        html[data-theme="dark"] .form-control::placeholder {
            color: #6e7c74 !important;
        }
        html[data-theme="dark"] select option {
            background-color: #12221e !important;
            color: #e2e8f0 !important;
        }
        html[data-theme="dark"] .pill-signal {
            background: rgba(20, 199, 154, 0.15) !important;
            color: var(--signal) !important;
        }
        html[data-theme="dark"] .pill-coral {
            background: rgba(232, 92, 85, 0.15) !important;
            color: #ff766f !important;
        }
        html[data-theme="dark"] .pill-amber {
            background: rgba(224, 160, 48, 0.15) !important;
            color: var(--amber) !important;
        }
        html[data-theme="dark"] .pill-steel {
            background: rgba(76, 126, 168, 0.15) !important;
            color: #72aae2 !important;
        }
        html[data-theme="dark"] .pill-muted {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
        }
        html[data-theme="dark"] a:not(.btn):not(.nav-link) {
            color: var(--signal) !important;
        }
        html[data-theme="dark"] a:not(.btn):not(.nav-link):hover {
            color: var(--signal-dark) !important;
        }
        html[data-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(20, 199, 154, 0.05) !important;
        }
        html[data-theme="dark"] .border,
        html[data-theme="dark"] .border-top,
        html[data-theme="dark"] .border-bottom,
        html[data-theme="dark"] .border-start,
        html[data-theme="dark"] .border-end {
            border-color: #1e3630 !important;
        }
        html[data-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }
        html[data-theme="dark"] .table {
            background-color: var(--card) !important;
            color: var(--text) !important;
            --bs-table-bg: var(--card) !important;
            --bs-table-hover-bg: rgba(20, 199, 154, 0.05) !important;
        }
        html[data-theme="dark"] .table td,
        html[data-theme="dark"] .table th {
            background-color: transparent !important;
        }

        /* Theme Buttons Styling on Settings Page */
        .theme-btn {
            border: none !important;
            color: var(--text-soft) !important;
            background-color: transparent !important;
        }
        .theme-btn:hover {
            color: var(--text) !important;
            background-color: rgba(110, 124, 116, 0.05) !important;
        }
        .theme-btn.active {
            color: var(--text) !important;
            background-color: var(--card) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
        }
        html[data-theme="dark"] .theme-btn.active {
            background-color: #172b26 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
        }

        /* ══════════════════════════════════════
           BASE
        ══════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: var(--font-body);
            background: var(--paper);
            color: var(--text);
            min-height: 100vh;
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--ink);
            position: fixed;
            top: 0; left: 0; z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform .25s ease;
            overflow-y: hidden;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }

        /* Brand area */
        .sb-brand {
            padding: 1.1rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            flex-shrink: 0;
        }
        .sb-brand-inner {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        /* Pulse icon */
        .sb-pulse-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
        }
        .sb-brand-text h5 {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: .95rem;
            color: #fff;
            margin: 0;
            line-height: 1.2;
        }
        .sb-brand-text small {
            font-family: var(--font-mono);
            font-weight: 500;
            font-size: .62rem;
            color: rgba(255,255,255,.45);
            letter-spacing: .04em;
        }

        /* SYS.LIVE badge */
        .sys-live {
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: .55rem 1.25rem .45rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .sys-live-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--signal);
            flex-shrink: 0;
            animation: blink-dot 1.4s ease-in-out infinite;
        }
        @media (prefers-reduced-motion: reduce) {
            .sys-live-dot { animation: none; }
        }
        @keyframes blink-dot {
            0%, 100% { opacity: 1; }
            50%       { opacity: .25; }
        }
        .sys-live-label {
            font-family: var(--font-mono);
            font-weight: 600;
            font-size: .6rem;
            color: var(--signal);
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        /* Nav labels */
        .sb-nav-label {
            font-family: var(--font-mono);
            font-weight: 600;
            font-size: .6rem;
            color: rgba(255,255,255,.3);
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .8rem 1.25rem .2rem;
        }

        /* Nav links */
        #sidebar .nav-link {
            font-family: var(--font-body);
            font-size: .84rem;
            font-weight: 400;
            color: rgba(255,255,255,.65);
            padding: .5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            border-left: 3px solid transparent;
            transition: background .15s, color .15s, border-color .15s;
            line-height: 1.4;
        }
        #sidebar .nav-link i {
            width: 18px;
            text-align: center;
            font-size: 1rem;
            opacity: .7;
        }
        #sidebar .nav-link:hover {
            background: rgba(255,255,255,.06);
            color: #fff;
            border-left-color: rgba(20,199,154,.4);
        }
        #sidebar .nav-link:hover i { opacity: 1; }
        #sidebar .nav-link.active {
            background: rgba(20,199,154,.1);
            color: #fff;
            border-left-color: var(--signal);
            font-weight: 600;
        }
        #sidebar .nav-link.active i { opacity: 1; }

        /* Logout button override */
        #sidebar .nav-link-logout {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
        }

        /* ── Sidebar User Menu & Logout styles ── */
        #sidebar .sb-group-toggle {
            transition: background-color 0.15s, border-color 0.15s, box-shadow 0.15s;
        }
        #sidebar .sb-group-toggle:hover {
            background: rgba(255,255,255,.08) !important;
            border-color: rgba(20,199,154,.35) !important;
            color: #fff;
        }
        #sidebar .nav-link-logout:hover {
            background: rgba(232, 92, 85, 0.18) !important;
            border-color: rgba(232, 92, 85, 0.4) !important;
            color: #ff6b6b !important;
        }

        /* ══════════════════════════════════════
           TOPBAR
        ══════════════════════════════════════ */
        #topbar {
            height: var(--topbar-height);
            background: var(--card);
            border-bottom: 1px solid var(--line);
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            gap: .75rem;
        }

        /* Hamburger / toggle */
        .topbar-toggle {
            display: none;
            background: none;
            border: 1px solid var(--line);
            border-radius: .4rem;
            padding: .3rem .5rem;
            color: var(--text-soft);
            cursor: pointer;
        }
        .topbar-toggle:focus-visible {
            outline: 2px solid var(--signal);
            outline-offset: 2px;
        }

        /* Search */
        .topbar-search {
            flex: 1;
            max-width: 340px;
        }
        .topbar-search input {
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: var(--paper);
            color: var(--text);
            font-family: var(--font-body);
            font-size: .83rem;
            padding: .38rem .75rem .38rem 2.1rem;
            width: 100%;
            transition: border-color .15s, box-shadow .15s;
        }
        .topbar-search input::placeholder { color: var(--text-soft); }
        .topbar-search input:focus {
            outline: none;
            border-color: var(--signal);
            box-shadow: 0 0 0 3px rgba(20,199,154,.18);
        }
        .topbar-search-wrap {
            position: relative;
        }
        .topbar-search-wrap i {
            position: absolute;
            left: .65rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-soft);
            font-size: .82rem;
            pointer-events: none;
        }


        /* Live Clock */
        .topbar-clock {
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .topbar-clock-date {
            font-family: var(--font-body);
            font-size: .72rem;
            font-weight: 600;
            color: var(--text);
            line-height: 1.2;
        }
        .topbar-clock-time {
            font-family: var(--font-mono);
            font-size: .7rem;
            font-weight: 600;
            color: var(--signal-dark);
            letter-spacing: .04em;
            line-height: 1.2;
        }

        /* Notifications */
        .topbar-notif {
            position: relative;
            background: none;
            border: 1px solid var(--line);
            border-radius: .4rem;
            width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-soft);
            cursor: pointer;
            transition: border-color .15s, color .15s;
        }
        .topbar-notif:hover { border-color: var(--signal); color: var(--signal); }
        .topbar-notif:focus-visible { outline: 2px solid var(--signal); outline-offset: 2px; }
        .notif-dot {
            position: absolute;
            top: 5px; right: 5px;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--coral);
        }

        /* Page title */
        .topbar-title {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: .95rem;
            color: var(--text);
        }

        /* User chip */
        .topbar-user {
            display: flex;
            align-items: center;
            gap: .55rem;
            background: none;
            border: 1px solid var(--line);
            border-radius: 2rem;
            padding: .25rem .75rem .25rem .25rem;
            cursor: pointer;
            transition: border-color .15s;
        }
        .topbar-user:hover { border-color: var(--signal); }
        .topbar-user:focus-visible { outline: 2px solid var(--signal); outline-offset: 2px; }
        .topbar-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: var(--signal);
            color: var(--ink);
            font-family: var(--font-display);
            font-weight: 700;
            font-size: .72rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .topbar-user-name {
            font-family: var(--font-body);
            font-size: .8rem;
            font-weight: 600;
            color: var(--text);
            line-height: 1;
        }
        .topbar-user-role {
            font-family: var(--font-mono);
            font-size: .6rem;
            font-weight: 500;
            color: var(--text-soft);
            line-height: 1;
            margin-top: 1px;
        }

        /* ══════════════════════════════════════
           PAGE TITLE BAR
        ══════════════════════════════════════ */
        #page-titlebar {
            position: fixed;
            top: var(--topbar-height);
            left: var(--sidebar-width);
            right: 0;
            z-index: 1020;
            height: 54px;
            background: var(--paper);
            border-bottom: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            padding: 0.4rem 1.25rem;
            gap: 2px;
        }
        #page-titlebar-text {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1rem;
            color: var(--text);
            white-space: nowrap;
            line-height: 1.2;
        }
        #page-titlebar .breadcrumb {
            margin-bottom: 0;
        }
        #page-titlebar .breadcrumb-item,
        #page-titlebar .breadcrumb-item a {
            color: var(--text-soft) !important;
            font-size: 0.72rem;
            text-decoration: none;
            opacity: 0.85;
        }
        #page-titlebar .breadcrumb-item.active {
            opacity: 0.7;
        }

        /* ══════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════ */
        #main-content {
            margin-left: var(--sidebar-width);
            padding-top: calc(var(--topbar-height) + 54px + 1.5rem);
            padding-bottom: 1.5rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ══════════════════════════════════════
           FOOTER
        ══════════════════════════════════════ */
        .app-footer {
            margin-top: auto;
            padding: 1.25rem 0 0.5rem;
            text-align: center;
            border-top: 1px solid var(--line);
        }
        .app-footer-text {
            font-family: var(--font-mono);
            font-size: .68rem;
            font-weight: 500;
            color: var(--text-soft);
            letter-spacing: .03em;
        }
        .app-footer-dot {
            display: inline-block;
            margin: 0 .4rem;
            color: var(--signal);
        }

        /* ══════════════════════════════════════
           STAT CARDS
        ══════════════════════════════════════ */
        .stat-card {
            background: var(--card);
            border-radius: .75rem;
            border: 1px solid var(--line);
            border-left: 3px solid var(--signal);
            padding: 1.25rem 1.25rem 1rem;
            box-shadow: 0 1px 4px rgba(10,31,28,.06);
            overflow: hidden;
        }
        .stat-card.card-signal { border-left-color: var(--signal); }
        .stat-card.card-coral  { border-left-color: var(--coral);  }
        .stat-card.card-amber  { border-left-color: var(--amber);  }
        .stat-card.card-steel  { border-left-color: var(--steel);  }

        .stat-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: .6rem;
        }
        .stat-value {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 2.1rem;
            color: var(--text);
            line-height: 1;
        }
        .stat-label {
            font-family: var(--font-body);
            font-size: .78rem;
            color: var(--text-soft);
            margin-top: .3rem;
        }
        .stat-icon-wrap {
            width: 40px; height: 40px;
            border-radius: .5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .stat-card.card-signal .stat-icon-wrap { background: rgba(20,199,154,.12); color: var(--signal-dark); }
        .stat-card.card-coral  .stat-icon-wrap { background: rgba(232,92,85,.12);  color: var(--coral); }
        .stat-card.card-amber  .stat-icon-wrap { background: rgba(224,160,48,.12); color: var(--amber); }
        .stat-card.card-steel  .stat-icon-wrap { background: rgba(76,126,168,.12); color: var(--steel); }

        /* Sparkline SVG */
        .stat-sparkline {
            display: block;
            width: 100%;
            height: 28px;
            margin-top: .5rem;
        }

        /* ══════════════════════════════════════
           CARDS
        ══════════════════════════════════════ */
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: .75rem;
            box-shadow: 0 1px 4px rgba(10,31,28,.05);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--line);
            font-family: var(--font-body);
            font-weight: 600;
            font-size: .88rem;
            color: var(--text);
            padding: .9rem 1.1rem;
        }
        .card-body { padding: 1rem 1.1rem; }

        /* ══════════════════════════════════════
           TABLES
        ══════════════════════════════════════ */
        .table th {
            font-family: var(--font-mono);
            font-size: .68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--text-soft);
            background: var(--paper);
            border-bottom: 1px solid var(--line);
            padding: .65rem 1rem;
        }
        .table td {
            font-family: var(--font-body);
            font-size: .85rem;
            vertical-align: middle;
            color: var(--text);
            border-bottom: 1px solid var(--line);
            padding: .65rem 1rem;
        }
        .table tbody tr:hover td { background: rgba(20,199,154,.04); }
        .table a { color: var(--signal-dark); text-decoration: none; font-weight: 500; }
        .table a:hover { text-decoration: underline; }

        /* ══════════════════════════════════════
           STATUS PILLS
        ══════════════════════════════════════ */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-family: var(--font-mono);
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            padding: .2rem .55rem;
            border-radius: 2rem;
            white-space: nowrap;
        }
        .pill-signal { background: rgba(20,199,154,.12);  color: var(--signal-dark); }
        .pill-coral  { background: rgba(232,92,85,.12);   color: var(--coral); }
        .pill-amber  { background: rgba(224,160,48,.12);  color: #a06800; }
        .pill-steel  { background: rgba(76,126,168,.12);  color: var(--steel); }
        .pill-muted  { background: rgba(110,124,116,.1);  color: var(--text-soft); }

        /* ══════════════════════════════════════
           BREADCRUMB
        ══════════════════════════════════════ */
        .breadcrumb {
            background: transparent;
            padding: 0;
            font-size: .78rem;
            font-family: var(--font-body);
        }
        .breadcrumb-item { color: var(--text-soft); }
        .breadcrumb-item.active { color: var(--text); font-weight: 500; }
        .breadcrumb-item + .breadcrumb-item::before { color: var(--line); }

        /* ══════════════════════════════════════
           TOAST CONTAINER & CUSTOM TOAST/ALERT
         ══════════════════════════════════════ */
        #toast-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 1090;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
            width: auto;
            max-width: 90vw;
        }
        #toast-container .custom-toast {
            pointer-events: auto;
            width: max-content !important;
            min-width: 320px;
            max-width: 100% !important;
            border-radius: 0.5rem !important;
            padding: 0 !important;
            box-shadow: 0 10px 30px rgba(10, 32, 28, 0.08) !important;
            background: #ffffff !important;
            border: 1px solid #E2E8F0 !important;
            
            /* CSS Transition for Smooth Entry & Exit */
            opacity: 0;
            transform: translateX(20px) scale(0.95);
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        #toast-container .custom-toast.show {
            opacity: 1 !important;
            transform: translateX(0) scale(1) !important;
        }
        .custom-toast .toast-icon {
            font-size: 1.4rem;
            align-self: flex-start;
            margin-top: 0.1rem;
        }
        .custom-toast .toast-title {
            font-family: var(--font-display);
            font-size: 0.88rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 0.15rem;
        }
        .custom-toast .toast-message {
            font-family: var(--font-body);
            font-size: 0.8rem;
            color: #475569;
            line-height: 1.4;
        }
        .custom-toast .btn-close {
            background-color: transparent !important;
            border: 0;
            opacity: 0.4;
            padding: 0.25rem !important;
            margin: 0 !important;
            font-size: 0.75rem;
            align-self: flex-start;
        }
        .custom-toast .btn-close:hover {
            opacity: 1;
        }

        /* Success Theme */
        .toast-success {
            background: #DCFCE7 !important;
            border-left: 4px solid #10B981 !important;
        }
        .toast-success .toast-icon {
            color: #10B981 !important;
        }

        /* Error Theme */
        .toast-error {
            background: #FEE2E2 !important;
            border-left: 4px solid #EF4444 !important;
        }
        .toast-error .toast-icon {
            color: #EF4444 !important;
        }

        /* Warning Theme */
        .toast-warning {
            background: #FEF3C7 !important;
            border-left: 4px solid #F59E0B !important;
        }
        .toast-warning .toast-icon {
            color: #F59E0B !important;
        }

        /* Information Theme */
        .toast-info {
            background: #DBEAFE !important;
            border-left: 4px solid #3B82F6 !important;
        }
        .toast-info .toast-icon {
            color: #3B82F6 !important;
        }

        /* Neutral Theme */
        .toast-neutral {
            background: #F1F5F9 !important;
            border-left: 4px solid #64748B !important;
        }
        .toast-neutral .toast-icon {
            color: #64748B !important;
        }

        /* ══════════════════════════════════════
           BOOTSTRAP ALERTS BEAUTIFICATION
         ══════════════════════════════════════ */
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
            font-family: var(--font-body);
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


        /* ══════════════════════════════════════
           FOCUS RINGS (global)
        ══════════════════════════════════════ */
        :focus-visible {
            outline: 2px solid var(--signal);
            outline-offset: 2px;
        }

        /* ══════════════════════════════════════
           SIDEBAR OVERLAY (mobile)
        ══════════════════════════════════════ */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10,31,28,.55);
            z-index: 1039;
        }
        #sidebar-overlay.show { display: block; }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 767px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.show {
                transform: translateX(0);
            }
            #topbar {
                left: 0;
            }
            #page-titlebar {
                left: 0;
            }
            #main-content {
                margin-left: 0;
            }
            .topbar-toggle {
                display: flex;
            }
            .topbar-search { max-width: 200px; }
            .topbar-title { display: none; }
        }

        /* ══════════════════════════════════════
           PRINT
        ══════════════════════════════════════ */
        @media print {
            #sidebar, #topbar, #toast-container { display: none !important; }
            #main-content { margin: 0; padding: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ═══ SIDEBAR OVERLAY (mobile) ═══ --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
<nav id="sidebar" aria-label="Main navigation">

    {{-- Brand --}}
    <div class="sb-brand">
        <div class="sb-brand-inner">
            {{-- Department Logo / Brand Icon --}}
            @php
                $userDept = strtolower(auth()->user()->department ?? '');
                $showSvgPulse = false;
                if (str_contains($userDept, 'admin')) {
                    $deptIcon = 'ph-fill ph-shield-checkered';
                } elseif (str_contains($userDept, 'medicine') || str_contains($userDept, 'doctor')) {
                    $deptIcon = 'ph-fill ph-heartbeat';
                } elseif (str_contains($userDept, 'lab')) {
                    $deptIcon = 'ph-fill ph-flask';
                } elseif (str_contains($userDept, 'radio')) {
                    $deptIcon = 'ph-fill ph-scan';
                } elseif (str_contains($userDept, 'pharm')) {
                    $deptIcon = 'ph-fill ph-pill';
                } elseif (str_contains($userDept, 'nutrition') || str_contains($userDept, 'diet')) {
                    $deptIcon = 'ph-fill ph-apple';
                } elseif (str_contains($userDept, 'operating') || str_contains($userDept, 'surgery') || str_contains($userDept, 'or')) {
                    $deptIcon = 'ph-fill ph-first-aid';
                } else {
                    $showSvgPulse = true;
                }
            @endphp
            @if($showSvgPulse)
                <svg class="sb-pulse-icon" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect width="34" height="34" rx="8" fill="rgba(20,199,154,0.12)"/>
                    <polyline
                        points="3,17 8,17 10,11 12,23 15,8 17,26 19,14 21,20 23,17 31,17"
                        fill="none"
                        stroke="#14C79A"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            @else
                <div class="sb-brand-icon-wrap" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(20,199,154,0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="{{ $deptIcon }}" style="color: var(--signal); font-size: 1.15rem;"></i>
                </div>
            @endif
            <div class="sb-brand-text">
                @php
                    $roleTagline = '';
                    $brandSuffix = 'DTCS';
                    $primaryRole = auth()->user()->primaryRole;
                    if ($primaryRole === 'admin') {
                        $roleTagline = 'Administrator';
                        $brandSuffix = 'Admin';
                    } elseif ($primaryRole === 'med-tech') {
                        $roleTagline = 'Laboratory';
                        $brandSuffix = 'LIS';
                    } elseif (in_array($primaryRole, ['rad-tech', 'radiologist'])) {
                        $roleTagline = 'radiology';
                        $brandSuffix = 'RIS';
                    } elseif ($primaryRole === 'pharmacist') {
                        $roleTagline = 'pharmacy';
                        $brandSuffix = 'PMS';
                    } elseif ($primaryRole === 'or-coordinator') {
                        $roleTagline = 'surgery';
                        $brandSuffix = 'SORS';
                    } elseif ($primaryRole === 'dietitian') {
                        $roleTagline = 'nutrition';
                        $brandSuffix = 'DNMS';
                    } elseif ($primaryRole === 'doctor') {
                        $roleTagline = 'doctor';
                        $brandSuffix = 'DOC';
                    }
                @endphp
                <h5>HIMS - {{ $brandSuffix }}</h5>
                @if($roleTagline)
                    <span class="brand-tagline" style="color: var(--signal); font-size: 0.72rem; font-weight: 600; display: block; margin-top: -3px; margin-bottom: 2px; text-transform: uppercase;">{{ $roleTagline }}</span>
                @endif
                <small>Hospital Operations Suite</small>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div style="flex:1; overflow-y:auto; padding-top:.5rem; -webkit-overflow-scrolling: touch;">
        @include('partials._sidebar')
    </div>

    {{-- User Account Menu (Stuck at the bottom) --}}
    <div class="px-3 py-3 border-top" style="border-color: rgba(255,255,255,0.06) !important; background: var(--ink); z-index: 1050; flex-shrink: 0;">
        <div class="d-flex flex-column gap-1">
            {{-- Direct User Profile Button --}}
            <a href="{{ route('profile.edit') }}" 
               class="nav-link sb-group-toggle w-100 text-start d-flex align-items-center justify-content-between p-2 rounded {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
               style="background: transparent; border: 1px solid transparent; text-decoration: none;"
               id="userMenuBtn" 
               aria-label="User profile">
                <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                    <div class="topbar-avatar" style="width: 30px; height: 30px; border-radius: 50%; background: var(--signal); color: var(--ink); font-family: var(--font-display); font-weight: 700; font-size: .75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="text-start" style="line-height: 1.2; min-width: 0;">
                        <div style="font-size: .8rem; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 145px;">{{ auth()->user()->name }}</div>
                        <div style="font-size: .65rem; color: rgba(255,255,255,.5); font-family: var(--font-mono); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 145px;">{{ auth()->user()->roleName }}</div>
                    </div>
                </div>
                <i class="bi bi-chevron-right" style="font-size: .75rem; color: rgba(255,255,255,.4);"></i>
            </a>


            {{-- Logout Button Below User Menu Btn --}}
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" 
                        class="nav-link py-2 px-2.5 rounded text-danger w-100 nav-link-logout d-flex align-items-center gap-2" 
                        style="font-size: 0.82rem; background: transparent; border: 1px solid transparent; transition: background 0.15s, border-color 0.15s;"
                        aria-label="Logout">
                    <i class="bi bi-box-arrow-right" style="font-size: 0.95rem;"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>

</nav>

{{-- ═══════════════════ TOPBAR ═══════════════════ --}}
<header id="topbar">

    {{-- Hamburger --}}
    <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">
        <i class="bi bi-list" style="font-size:1.1rem;"></i>
    </button>

    {{-- App Identity (Department) --}}
    <div class="topbar-identity d-none d-md-flex flex-column justify-content-center ms-2" style="line-height: 1.2;">
        <div style="font-family: var(--font-display); font-size: 0.92rem; font-weight: 700; color: var(--ink);">
            {{ auth()->user()->department ?? 'General Operations' }}
        </div>
        <div style="font-size: 0.65rem; color: var(--text-soft); font-weight: 500; letter-spacing: 0.02em;">
            Hospital Operations Suite
        </div>
    </div>

    {{-- Left spacer to push search to the middle --}}
    <div class="flex-fill d-none d-sm-block"></div>

    {{-- Search --}}
    <div class="topbar-search d-none d-sm-block">
        <div class="topbar-search-wrap">
            <i class="bi bi-search"></i>
            <input type="search" placeholder="Search patients, records…" aria-label="Search">
        </div>
    </div>

    {{-- Right spacer to align search perfectly --}}
    <div class="flex-fill d-none d-sm-block"></div>

    {{-- Right actions: Clock, Messages, Notifications --}}
    <div class="d-flex align-items-center gap-2">
        {{-- Live Clock --}}
        <div class="topbar-clock d-none d-sm-flex" aria-label="Current date and time" aria-live="off">
            <div>
                <div class="topbar-clock-date" id="topbar-date"></div>
                <div class="topbar-clock-time" id="topbar-time"></div>
            </div>
        </div>

        {{-- Messages --}}
        <button class="topbar-notif" aria-label="Messages">
            <i class="bi bi-chat-left-text" style="font-size:.9rem;"></i>
        </button>

        {{-- Notifications --}}
        <button class="topbar-notif" aria-label="Notifications">
            <i class="bi bi-bell" style="font-size:.9rem;"></i>
            <span class="notif-dot" aria-hidden="true"></span>
        </button>
    </div>
</header>

{{-- ═══ PAGE TITLE BAR ═══ --}}
<div id="page-titlebar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @hasSection('breadcrumb')
                @yield('breadcrumb')
            @else
                @php
                    $user = Auth::user();
                    $role = $user?->primaryRole;
                    $breadcrumbs = [];
                    
                    $labActive   = request()->routeIs('lab.*');
                    $radActive   = request()->routeIs('radiology.*');
                    $pmsActive   = request()->routeIs('pharmacy.*');
                    $sorActive   = request()->routeIs('surgery.*');
                    $dnmActive   = request()->routeIs('diet.*');
                    $adminActive = request()->routeIs('admin.*') || request()->routeIs('admin.*.*');
                    
                    if ($role === 'admin') {
                        if ($labActive || $radActive || $pmsActive || $sorActive || $dnmActive) {
                            $breadcrumbs[] = ['title' => 'Modules'];
                        }
                        
                        if ($labActive) {
                            $breadcrumbs[] = ['title' => 'Laboratory (LIS)', 'url' => route('lab.dashboard')];
                        } elseif ($radActive) {
                            $breadcrumbs[] = ['title' => 'Radiology (RIS)', 'url' => route('radiology.dashboard')];
                        } elseif ($pmsActive) {
                            $breadcrumbs[] = ['title' => 'Pharmacy (PMS)', 'url' => route('pharmacy.dashboard')];
                        } elseif ($sorActive) {
                            $breadcrumbs[] = ['title' => 'Surgery (SORS)', 'url' => route('surgery.dashboard')];
                        } elseif ($dnmActive) {
                            $breadcrumbs[] = ['title' => 'Nutrition (DNMS)', 'url' => route('diet.dashboard')];
                        }
                        
                        if ($adminActive) {
                            $breadcrumbs[] = ['title' => 'Administration'];
                            if (request()->routeIs('admin.dashboard')) {
                                $breadcrumbs[] = ['title' => 'Dashboard', 'url' => route('admin.dashboard')];
                            } elseif (request()->routeIs('admin.users.*')) {
                                $breadcrumbs[] = ['title' => 'User Management', 'url' => route('admin.users.index')];
                            } elseif (request()->routeIs('admin.roles.*')) {
                                $breadcrumbs[] = ['title' => 'Permission', 'url' => route('admin.roles.index')];
                            }
                        }
                    } else {
                        // Non-admin roles
                        if ($labActive) {
                            $breadcrumbs[] = ['title' => 'Laboratory (LIS)', 'url' => route('lab.dashboard')];
                        } elseif ($radActive) {
                            $breadcrumbs[] = ['title' => 'Radiology (RIS)', 'url' => route('radiology.dashboard')];
                        } elseif ($pmsActive) {
                            $breadcrumbs[] = ['title' => 'Pharmacy (PMS)', 'url' => route('pharmacy.dashboard')];
                        } elseif ($sorActive) {
                            $breadcrumbs[] = ['title' => 'Surgery (SORS)', 'url' => route('surgery.dashboard')];
                        } elseif ($dnmActive) {
                            $breadcrumbs[] = ['title' => 'Nutrition (DNMS)', 'url' => route('diet.dashboard')];
                        }
                    }
                    
                    // Add specific page link
                    if ($labActive && !request()->routeIs('lab.dashboard')) {
                        if (request()->routeIs('lab.requests.*')) {
                            $breadcrumbs[] = ['title' => 'Lab Requests', 'url' => route('lab.requests.index')];
                        } elseif (request()->routeIs('lab.results.*')) {
                            $breadcrumbs[] = ['title' => 'Lab Results', 'url' => route('lab.results.index')];
                        }
                    } elseif ($radActive && !request()->routeIs('radiology.dashboard')) {
                        if (request()->routeIs('radiology.requests.*')) {
                            $breadcrumbs[] = ['title' => 'Imaging Requests', 'url' => route('radiology.requests.index')];
                        } elseif (request()->routeIs('radiology.reports.*')) {
                            $breadcrumbs[] = ['title' => 'Reports', 'url' => route('radiology.reports.index')];
                        }
                    } elseif ($pmsActive && !request()->routeIs('pharmacy.dashboard')) {
                        if (request()->routeIs('pharmacy.prescriptions.*')) {
                            $breadcrumbs[] = ['title' => 'Prescriptions', 'url' => route('pharmacy.prescriptions.index')];
                        } elseif (request()->routeIs('pharmacy.dispensing.*')) {
                            $breadcrumbs[] = ['title' => 'Dispensing', 'url' => route('pharmacy.dispensing.index')];
                        }
                    } elseif ($sorActive && !request()->routeIs('surgery.dashboard')) {
                        if (request()->routeIs('surgery.requests.*')) {
                            $breadcrumbs[] = ['title' => 'Surgery Requests', 'url' => route('surgery.requests.index')];
                        } elseif (request()->routeIs('surgery.schedules.*')) {
                            $breadcrumbs[] = ['title' => 'OR Schedules', 'url' => route('surgery.schedules.index')];
                        } elseif (request()->routeIs('surgery.calendar')) {
                            $breadcrumbs[] = ['title' => 'Surgery Calendar', 'url' => route('surgery.calendar')];
                        }
                    } elseif ($dnmActive && !request()->routeIs('diet.dashboard')) {
                        if (request()->routeIs('diet.requests.*')) {
                            $breadcrumbs[] = ['title' => 'Diet Requests', 'url' => route('diet.requests.index')];
                        } elseif (request()->routeIs('diet.plans.*')) {
                            $breadcrumbs[] = ['title' => 'Diet Plans', 'url' => route('diet.plans.index')];
                        }
                    }
                    
                    // Add Action Level
                    if (request()->routeIs('*.create')) {
                        $breadcrumbs[] = ['title' => 'Create'];
                    } elseif (request()->routeIs('*.edit')) {
                        $breadcrumbs[] = ['title' => 'Edit'];
                    } elseif (request()->routeIs('*.show')) {
                        $breadcrumbs[] = ['title' => 'Details'];
                    }
                    
                    // Doctor Dashboard fallback
                    if (request()->routeIs('doctor.dashboard')) {
                        $breadcrumbs[] = ['title' => 'Dashboard'];
                    }
                    
                    // Profile Page fallback
                    if (request()->routeIs('profile.*')) {
                        $breadcrumbs[] = ['title' => 'Account'];
                        $breadcrumbs[] = ['title' => 'Profile', 'url' => route('profile.edit')];
                    }
                    
                    // Patients Pages fallback
                    if (request()->routeIs('patients.*')) {
                        $breadcrumbs[] = ['title' => 'Patients', 'url' => route('patients.index')];
                        if (request()->routeIs('patients.create')) {
                            $breadcrumbs[] = ['title' => 'New Patient'];
                        } elseif (request()->routeIs('patients.edit')) {
                            $breadcrumbs[] = ['title' => 'Edit Patient'];
                        } elseif (request()->routeIs('patients.show')) {
                            $breadcrumbs[] = ['title' => 'Details'];
                        }
                    }
                @endphp
                @foreach($breadcrumbs as $index => $crumb)
                    @if($index === count($breadcrumbs) - 1)
                        <li class="breadcrumb-item active" aria-current="page">{{ $crumb['title'] }}</li>
                    @else
                        @if(isset($crumb['url']))
                            <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a></li>
                        @else
                            <li class="breadcrumb-item opacity-75">{{ $crumb['title'] }}</li>
                        @endif
                    @endif
                @endforeach
            @endif
        </ol>
    </nav>
    <div id="page-titlebar-text">@yield('page-title', 'Dashboard')</div>
</div>

{{-- ═══ TOAST MESSAGES ═══ --}}
<div id="toast-container">
    @include('partials._flash')
</div>

{{-- Page Intro Skeleton loader --}}
<div id="page-intro-skeleton" class="container-fluid" style="margin-left: var(--sidebar-width); padding-top: calc(var(--topbar-height) + 54px + 1.5rem); padding-left: 1.5rem; padding-right: 1.5rem;">
    @hasSection('skeleton')
        @yield('skeleton')
    @else
        @php
            $route = request()->route();
            $routeName = $route ? $route->getName() : '';
            $uri = request()->path();
        @endphp

        @if(str_contains($routeName, '.index') || str_contains($uri, '/index') || (str_contains($uri, 'patients') && !str_contains($uri, 'create') && !str_contains($uri, 'edit') && $routeName !== 'patients.show'))
            <x-skeleton.table :rows="5" :cols="5" />
        @elseif(str_contains($routeName, '.edit') || str_contains($routeName, '.create') || str_contains($uri, '/edit') || str_contains($uri, '/create'))
            <x-skeleton.form :fields="6" :columns="2" />
        @elseif(str_contains($routeName, 'profile') || str_contains($uri, 'profile'))
            <x-skeleton.profile />
        @else
            <x-skeleton.dashboard :stats="4" :cards="2" />
        @endif
    @endif
</div>

{{-- ═══════════════════ MAIN CONTENT ═══════════════════ --}}
<main id="main-content" class="d-none">
    @yield('content')

    {{-- ═══ APP FOOTER ═══ --}}
    <footer class="app-footer">
        @php
            $footerModule = 'General Operations';
            $footerRole = auth()->user()->primaryRole;
            if ($footerRole === 'admin') {
                $footerModule = 'System Administration';
            } elseif ($footerRole === 'med-tech') {
                $footerModule = 'Laboratory Information System';
            } elseif (in_array($footerRole, ['rad-tech', 'radiologist'])) {
                $footerModule = 'Radiology Information System';
            } elseif ($footerRole === 'pharmacist') {
                $footerModule = 'Pharmacy Management System';
            } elseif ($footerRole === 'or-coordinator') {
                $footerModule = 'Surgery & Operating Room Scheduler';
            } elseif ($footerRole === 'dietitian') {
                $footerModule = 'Diet & Nutrition Management System';
            } elseif ($footerRole === 'doctor') {
                $footerModule = 'Clinical Services';
            }
        @endphp
        <div class="app-footer-text">
            Hospital Information Management System
            <span class="app-footer-dot">&middot;</span>
            {{ $footerModule }}
        </div>
    </footer>
</main>

{{-- Full Screen Cardio Loader Overlay --}}
<div id="cardio-loader-overlay" style="display: none; position: fixed; inset: 0; background: rgba(10, 31, 28, 0.45); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; flex-direction: column;">
    <l-cardio size="100" stroke="6" speed="2" color="#14C79A"></l-cardio>
    <div style="margin-top: 1rem; font-family: 'Space Grotesk', sans-serif; font-weight: 600; color: #FFFFFF; font-size: 1.1rem; letter-spacing: 0.05em;">Loading...</div>
</div>

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /* ── Sidebar toggle (mobile) ── */
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const toggle   = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        toggle && toggle.setAttribute('aria-expanded', 'true');
    }
    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        toggle && toggle.setAttribute('aria-expanded', 'false');
    }
    toggle?.addEventListener('click', () => {
        sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
    });

    /* ── Bootstrap Toasts ── */
    document.querySelectorAll('.toast').forEach(el => {
        new bootstrap.Toast(el, { delay: 4500 }).show();
    });

    /* ── Global Alert Icons Injector ── */
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

    /* ── Confirmation dialogs ── */
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });

    /* ── Live Clock ── */
    (function tickClock() {
        const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        function pad(n) { return String(n).padStart(2, '0'); }
        function update() {
            const now  = new Date();
            const day  = days[now.getDay()];
            const date = now.getDate();
            const mon  = months[now.getMonth()];
            const yr   = now.getFullYear();
            let   h    = now.getHours();
            const m    = pad(now.getMinutes());
            const s    = pad(now.getSeconds());
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            const dateEl = document.getElementById('topbar-date');
            const timeEl = document.getElementById('topbar-time');
            if (dateEl) dateEl.textContent = `${day}, ${mon} ${date}, ${yr}`;
            if (timeEl) timeEl.textContent = `${pad(h)}:${m}:${s} ${ampm}`;
        }
        update();
        setInterval(update, 1000);
    })();
</script>

{{-- Skeleton Loading System --}}
<script src="{{ asset('js/skeleton.js') }}"></script>

{{-- Page Intro Loader Logic --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const intro = document.getElementById('page-intro-skeleton');
        const main = document.getElementById('main-content');
        if (intro && main) {
            // Check if page was loaded after a button click/form submission
            if (sessionStorage.getItem('submitted_form') === 'true') {
                sessionStorage.removeItem('submitted_form');
                intro.remove();
                main.classList.remove('d-none');
            } else {
                setTimeout(function() {
                    intro.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    intro.style.opacity = '0';
                    intro.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        intro.remove();
                        main.classList.remove('d-none');
                        main.style.animation = 'sk-fade-in 0.3s ease-out forwards';
                    }, 250);
                }, 300);
            }
        }
    });
    /* ── Theme Switcher JS Logic ── */
    document.addEventListener('DOMContentLoaded', function() {
        const themeBtns = document.querySelectorAll('[data-theme-val]');
        const savedSetting = localStorage.getItem('hims_theme') || 'system';

        function applyTheme(setting) {
            let actualTheme = setting;
            if (setting === 'system') {
                actualTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-theme', actualTheme);
            
            themeBtns.forEach(btn => {
                const val = btn.getAttribute('data-theme-val');
                if (val === setting) {
                    btn.classList.add('active');
                    btn.style.background = '';
                    btn.style.color = '';
                    btn.style.borderColor = '';
                } else {
                    btn.classList.remove('active');
                    btn.style.background = '';
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }
            });
        }

        themeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const selected = this.getAttribute('data-theme-val');
                localStorage.setItem('hims_theme', selected);
                applyTheme(selected);
            });
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
            if ((localStorage.getItem('hims_theme') || 'system') === 'system') {
                applyTheme('system');
            }
        });

        applyTheme(savedSetting);
    });
</script>

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
            sessionStorage.setItem('submitted_form', 'true');
            const overlay = document.getElementById('cardio-loader-overlay');
            if (overlay) {
                overlay.style.display = 'flex';
            }
            setTimeout(() => { btn.disabled = true; }, 10);
        }
    });

    // Intercept clicks on links styled as .btn that trigger navigation
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (link && link.classList.contains('btn') && !link.hasAttribute('data-bs-toggle') && link.getAttribute('href') && link.getAttribute('href') !== '#') {
            sessionStorage.setItem('submitted_form', 'true');
            const overlay = document.getElementById('cardio-loader-overlay');
            if (overlay) {
                overlay.style.display = 'flex';
            }
        }
    });
</script>



<script>
// Apply data-width to progress bar fill elements (avoids Blade-in-CSS IDE errors)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.progress-bar-fill[data-width]').forEach(function (el) {
        el.style.width = el.getAttribute('data-width') + '%';
    });
});
</script>

@stack('scripts')
</body>
</html>
