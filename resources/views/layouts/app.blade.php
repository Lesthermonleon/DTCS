<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js'])
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
            document.documentElement.setAttribute('data-bs-theme', theme);
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
            --amber:        #64748B;
            --steel:        #475569;

            --color-primary:        #14C79A;
            --color-primary-dark:   #0C8F6F;
            --color-primary-light:  rgba(20, 199, 154, 0.12);
            --color-danger:         #E85C55;
            --color-danger-dark:    #DC2626;
            --color-danger-light:   rgba(232, 92, 85, 0.12);
            --color-background:     #F7F5F0;
            --color-surface:        #FFFFFF;
            --color-border:         #E6E2D6;
            --color-text:           #132420;
            --color-text-muted:     #6E7C74;

            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --sidebar-current-width: var(--sidebar-width);
            --topbar-height: 60px;
            --font-display: 'Space Grotesk', sans-serif;
            --font-body:    'Inter', sans-serif;
            --font-mono:    'IBM Plex Mono', monospace;

            /* Sidebar Light Mode variables */
            --sidebar-bg:          #FFFFFF;
            --sidebar-border:      var(--line);
            --sidebar-text:        var(--text);
            --sidebar-text-soft:   var(--text-soft);
            --sidebar-hover-bg:    rgba(20, 199, 154, 0.08);
            --sidebar-hover-text:  var(--text);
            --sidebar-active-bg:   rgba(20, 199, 154, 0.12);
            --sidebar-active-text: var(--text);
        }

        body.sidebar-collapsed {
            --sidebar-current-width: var(--sidebar-collapsed-width);
        }

        /* Dark Theme Overrides */
        html[data-theme="dark"] {
            --paper:        #0B1412;
            --card:         #12221E;
            --line:         #1E3630;
            --text:         #E2E8F0;
            --text-soft:    #94A3B8;

            /* Sidebar Dark Mode variables */
            --sidebar-bg:          var(--ink);
            --sidebar-border:      rgba(255, 255, 255, 0.07);
            --sidebar-text:        #ffffff;
            --sidebar-text-soft:   rgba(255, 255, 255, 0.55);
            --sidebar-hover-bg:    rgba(255, 255, 255, 0.06);
            --sidebar-hover-text:  #ffffff;
            --sidebar-active-bg:   rgba(20, 199, 154, 0.1);
            --sidebar-active-text: #ffffff;
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

        /* ══════════════════════════════════════
           THEME CONTRAST & VISIBILITY OVERRIDES (LIGHT & DARK)
        ══════════════════════════════════════ */
        
        /* ── Light Mode Text & Component Contrast ── */
        html[data-theme="light"],
        html[data-bs-theme="light"],
        :root:not([data-theme="dark"]) {
            --text-soft: #4a5c54;
        }
        html[data-theme="light"] .text-soft,
        html[data-bs-theme="light"] .text-soft,
        :root:not([data-theme="dark"]) .text-soft {
            color: #4a5c54 !important;
        }
        html[data-theme="light"] .text-muted,
        html[data-bs-theme="light"] .text-muted,
        :root:not([data-theme="dark"]) .text-muted {
            color: #55655d !important;
        }
        html[data-theme="light"] .text-secondary,
        html[data-bs-theme="light"] .text-secondary,
        :root:not([data-theme="dark"]) .text-secondary {
            color: #4a5c54 !important;
        }
        html[data-theme="light"] .text-dark,
        html[data-bs-theme="light"] .text-dark,
        :root:not([data-theme="dark"]) .text-dark {
            color: #0A1F1C !important;
        }
        html[data-theme="light"] .text-body,
        html[data-bs-theme="light"] .text-body,
        :root:not([data-theme="dark"]) .text-body {
            color: #132420 !important;
        }

        /* ── Dark Mode Text, Background & Element Visibility ── */
        html[data-theme="dark"],
        html[data-bs-theme="dark"] {
            --paper:        #0B1412;
            --card:         #12221E;
            --line:         #1E3630;
            --text:         #E2E8F0;
            --text-soft:    #94A3B8;
        }
        html[data-theme="dark"] body,
        html[data-bs-theme="dark"] body {
            background-color: #0B1412 !important;
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .text-dark,
        html[data-bs-theme="dark"] .text-dark,
        html[data-theme="dark"] .text-black,
        html[data-bs-theme="dark"] .text-black,
        html[data-theme="dark"] .text-body-emphasis,
        html[data-bs-theme="dark"] .text-body-emphasis {
            color: #F8FAFC !important;
        }
        html[data-theme="dark"] .text-body,
        html[data-bs-theme="dark"] .text-body {
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .text-body-secondary,
        html[data-bs-theme="dark"] .text-body-secondary,
        html[data-theme="dark"] .text-secondary,
        html[data-bs-theme="dark"] .text-secondary,
        html[data-theme="dark"] .text-soft,
        html[data-bs-theme="dark"] .text-soft,
        html[data-theme="dark"] .text-muted,
        html[data-bs-theme="dark"] .text-muted {
            color: #94A3B8 !important;
        }

        /* Override inline dark text colors when in dark mode */
        html[data-theme="dark"] [style*="color: #132420"],
        html[data-theme="dark"] [style*="color:#132420"],
        html[data-theme="dark"] [style*="color: #0A1F1C"],
        html[data-theme="dark"] [style*="color:#0A1F1C"],
        html[data-theme="dark"] [style*="color: #1b1b18"],
        html[data-theme="dark"] [style*="color:#1b1b18"],
        html[data-theme="dark"] [style*="color: #212529"],
        html[data-theme="dark"] [style*="color:#212529"],
        html[data-theme="dark"] [style*="color: black"],
        html[data-theme="dark"] [style*="color:black"],
        html[data-theme="dark"] [style*="color: #000"],
        html[data-theme="dark"] [style*="color:#000"] {
            color: #E2E8F0 !important;
        }

        /* Override inline white backgrounds when in dark mode */
        html[data-theme="dark"] [style*="background: #ffffff"],
        html[data-theme="dark"] [style*="background:#ffffff"],
        html[data-theme="dark"] [style*="background: #fff"],
        html[data-theme="dark"] [style*="background:#fff"],
        html[data-theme="dark"] [style*="background: white"],
        html[data-theme="dark"] [style*="background:white"],
        html[data-theme="dark"] [style*="background-color: #ffffff"],
        html[data-theme="dark"] [style*="background-color: #fff"],
        html[data-theme="dark"] [style*="background-color: white"] {
            background-color: #12221E !important;
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .bg-white,
        html[data-bs-theme="dark"] .bg-white {
            background-color: #12221E !important;
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .bg-light,
        html[data-bs-theme="dark"] .bg-light,
        html[data-theme="dark"] .bg-body-tertiary,
        html[data-bs-theme="dark"] .bg-body-tertiary {
            background-color: #172D28 !important;
            color: #E2E8F0 !important;
        }

        /* Cards, Modals, Offcanvas & Dropdowns */
        html[data-theme="dark"] .card,
        html[data-bs-theme="dark"] .card,
        html[data-theme="dark"] .modal-content,
        html[data-bs-theme="dark"] .modal-content,
        html[data-theme="dark"] .offcanvas,
        html[data-bs-theme="dark"] .offcanvas,
        html[data-theme="dark"] .dropdown-menu,
        html[data-bs-theme="dark"] .dropdown-menu {
            background-color: #12221E !important;
            border-color: #1E3630 !important;
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .card-header,
        html[data-theme="dark"] .card-footer,
        html[data-theme="dark"] .modal-header,
        html[data-theme="dark"] .modal-footer,
        html[data-theme="dark"] .offcanvas-header,
        html[data-bs-theme="dark"] .card-header,
        html[data-bs-theme="dark"] .card-footer,
        html[data-bs-theme="dark"] .modal-header,
        html[data-bs-theme="dark"] .modal-footer,
        html[data-bs-theme="dark"] .offcanvas-header {
            border-color: #1E3630 !important;
            color: #F8FAFC !important;
        }
        html[data-theme="dark"] .dropdown-item,
        html[data-bs-theme="dark"] .dropdown-item {
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .dropdown-item:hover,
        html[data-bs-theme="dark"] .dropdown-item:hover,
        html[data-theme="dark"] .dropdown-item:focus,
        html[data-bs-theme="dark"] .dropdown-item:focus {
            background-color: rgba(20, 199, 154, 0.12) !important;
            color: var(--signal) !important;
        }
        html[data-theme="dark"] .dropdown-divider,
        html[data-bs-theme="dark"] .dropdown-divider {
            border-top-color: #1E3630 !important;
        }

        /* Form Controls & Inputs */
        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select,
        html[data-bs-theme="dark"] .form-control,
        html[data-bs-theme="dark"] .form-select {
            background-color: #0B1412 !important;
            border-color: #1E3630 !important;
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .form-control:focus,
        html[data-theme="dark"] .form-select:focus,
        html[data-bs-theme="dark"] .form-control:focus,
        html[data-bs-theme="dark"] .form-select:focus {
            background-color: #12221E !important;
            border-color: var(--signal) !important;
            box-shadow: 0 0 0 0.25rem rgba(20, 199, 154, 0.25) !important;
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .form-control::placeholder,
        html[data-bs-theme="dark"] .form-control::placeholder {
            color: #6E7C74 !important;
        }
        html[data-theme="dark"] .form-label,
        html[data-bs-theme="dark"] .form-label,
        html[data-theme="dark"] .form-check-label,
        html[data-bs-theme="dark"] .form-check-label {
            color: #E2E8F0 !important;
        }
        html[data-theme="dark"] .input-group-text,
        html[data-bs-theme="dark"] .input-group-text {
            background-color: #172B26 !important;
            border-color: #1E3630 !important;
            color: #94A3B8 !important;
        }
        html[data-theme="dark"] select option,
        html[data-bs-theme="dark"] select option {
            background-color: #12221E !important;
            color: #E2E8F0 !important;
        }

        /* Status Pills & Badges - Strictly Restricted Palette (Green, Dark Green, Red, Monochromatic) */
        .pill-signal {
            background: rgba(20, 199, 154, 0.12) !important;
            color: var(--signal-dark) !important;
        }
        .pill-coral {
            background: rgba(232, 92, 85, 0.12) !important;
            color: var(--coral) !important;
        }
        .pill-amber,
        .pill-steel,
        .pill-muted {
            background: rgba(100, 116, 139, 0.1) !important;
            color: #475569 !important;
        }
        html[data-theme="dark"] .pill-signal {
            background: rgba(20, 199, 154, 0.15) !important;
            color: var(--signal) !important;
        }
        html[data-theme="dark"] .pill-coral {
            background: rgba(232, 92, 85, 0.15) !important;
            color: #FF766F !important;
        }
        html[data-theme="dark"] .pill-amber,
        html[data-theme="dark"] .pill-steel,
        html[data-theme="dark"] .pill-muted {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #CBD5E1 !important;
        }

        /* ══════════════════════════════════════
           SYSTEM-WIDE PALETTE OVERRIDES
           (Green, Dark Green, Red & Monochromatic)
        ══════════════════════════════════════ */
        /* Neutralize Amber / Yellow Badges & Backgrounds */
        .bg-warning, .bg-warning-subtle {
            background-color: rgba(100, 116, 139, 0.12) !important;
            color: #475569 !important;
        }
        .text-warning, .text-warning-emphasis {
            color: #475569 !important;
        }
        .border-warning, .border-warning-subtle {
            border-color: rgba(100, 116, 139, 0.3) !important;
        }
        .badge.bg-warning, .badge.bg-warning-subtle {
            background-color: rgba(100, 116, 139, 0.12) !important;
            color: #334155 !important;
            border: 1px solid rgba(100, 116, 139, 0.2) !important;
        }

        /* Neutralize Info / Blue Badges & Backgrounds */
        .bg-info, .bg-info-subtle {
            background-color: rgba(71, 85, 105, 0.1) !important;
            color: #334155 !important;
        }
        .text-info, .text-info-emphasis {
            color: #334155 !important;
        }
        .border-info, .border-info-subtle {
            border-color: rgba(71, 85, 105, 0.25) !important;
        }
        .badge.bg-info, .badge.bg-info-subtle {
            background-color: rgba(71, 85, 105, 0.1) !important;
            color: #334155 !important;
            border: 1px solid rgba(71, 85, 105, 0.2) !important;
        }

        /* Neutralize Purple / Violet / Indigo / Cyan / Orange / Pink */
        .bg-purple, .bg-purple-subtle,
        .bg-indigo, .bg-indigo-subtle,
        .bg-cyan, .bg-cyan-subtle,
        .bg-orange, .bg-orange-subtle,
        .bg-pink, .bg-pink-subtle {
            background-color: rgba(100, 116, 139, 0.1) !important;
            color: #334155 !important;
        }
        .text-purple, .text-indigo, .text-cyan, .text-orange, .text-pink {
            color: #475569 !important;
        }
        .badge.bg-purple, .badge.bg-indigo, .badge.bg-cyan, .badge.bg-orange, .badge.bg-pink {
            background-color: rgba(100, 116, 139, 0.12) !important;
            color: #334155 !important;
            border: 1px solid rgba(100, 116, 139, 0.2) !important;
        }

        /* Map Success / Primary to Signal Green & Dark Green */
        .bg-success, .bg-success-subtle {
            background-color: rgba(20, 199, 154, 0.12) !important;
            color: var(--signal-dark) !important;
        }
        .text-success {
            color: var(--signal-dark) !important;
        }
        html[data-theme="dark"] .text-success {
            color: var(--signal) !important;
        }

        /* Dark Mode Color Overrides for all non-green/red elements */
        html[data-theme="dark"] .bg-warning,
        html[data-theme="dark"] .bg-warning-subtle,
        html[data-theme="dark"] .badge.bg-warning,
        html[data-theme="dark"] .badge.bg-warning-subtle,
        html[data-theme="dark"] .bg-info,
        html[data-theme="dark"] .bg-info-subtle,
        html[data-theme="dark"] .badge.bg-info,
        html[data-theme="dark"] .badge.bg-info-subtle,
        html[data-theme="dark"] .bg-purple,
        html[data-theme="dark"] .badge.bg-purple,
        html[data-theme="dark"] .bg-indigo,
        html[data-theme="dark"] .bg-cyan,
        html[data-theme="dark"] .bg-orange,
        html[data-theme="dark"] .bg-pink {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }
        html[data-theme="dark"] .text-warning,
        html[data-theme="dark"] .text-warning-emphasis,
        html[data-theme="dark"] .text-info,
        html[data-theme="dark"] .text-info-emphasis,
        html[data-theme="dark"] .text-purple,
        html[data-theme="dark"] .text-indigo,
        html[data-theme="dark"] .text-cyan,
        html[data-theme="dark"] .text-orange,
        html[data-theme="dark"] .text-pink {
            color: #cbd5e1 !important;
        }

        html[data-theme="dark"] a:not(.btn):not(.nav-link) {
            color: var(--signal) !important;
        }
        html[data-theme="dark"] a:not(.btn):not(.nav-link):hover {
            color: var(--signal-dark) !important;
        }

        /* Tables & Borders */
        html[data-theme="dark"] .table,
        html[data-bs-theme="dark"] .table {
            background-color: #12221E !important;
            color: #E2E8F0 !important;
            --bs-table-bg: #12221E !important;
            --bs-table-color: #E2E8F0 !important;
            --bs-table-hover-bg: rgba(20, 199, 154, 0.05) !important;
            --bs-table-border-color: #1E3630 !important;
        }
        html[data-theme="dark"] .table td,
        html[data-theme="dark"] .table th,
        html[data-bs-theme="dark"] .table td,
        html[data-bs-theme="dark"] .table th {
            background-color: transparent !important;
            border-bottom-color: #1E3630 !important;
        }
        html[data-theme="dark"] .table th,
        html[data-bs-theme="dark"] .table th {
            color: #94A3B8 !important;
        }
        html[data-theme="dark"] .table-hover tbody tr:hover,
        html[data-bs-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(20, 199, 154, 0.05) !important;
        }
        html[data-theme="dark"] .border,
        html[data-theme="dark"] .border-top,
        html[data-theme="dark"] .border-bottom,
        html[data-theme="dark"] .border-start,
        html[data-theme="dark"] .border-end,
        html[data-bs-theme="dark"] .border,
        html[data-bs-theme="dark"] .border-top,
        html[data-bs-theme="dark"] .border-bottom,
        html[data-bs-theme="dark"] .border-start,
        html[data-bs-theme="dark"] .border-end {
            border-color: #1E3630 !important;
        }

        /* ── Responsive Data Tables Styling ── */
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            margin-bottom: 0;
        }
        @media (max-width: 767.98px) {
            .table-responsive .table {
                min-width: 640px;
            }
            .table-responsive .table th,
            .table-responsive .table td {
                font-size: 0.85rem;
                padding: 0.5rem 0.6rem;
            }
            .table-responsive .table th.text-nowrap,
            .table-responsive .table td.text-nowrap {
                white-space: nowrap !important;
            }
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
            width: var(--sidebar-current-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            position: fixed;
            top: 0; left: 0; z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform .25s ease, width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: hidden;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            white-space: nowrap;
        }

        /* Brand area */
        .sb-brand {
            padding: 1.1rem 1.25rem 1rem;
            border-bottom: 1px solid var(--sidebar-border);
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
            color: var(--sidebar-text);
            margin: 0;
            line-height: 1.2;
        }
        .sb-brand-text small {
            font-family: var(--font-mono);
            font-weight: 500;
            font-size: .62rem;
            color: var(--sidebar-text-soft);
            letter-spacing: .04em;
        }

        /* SYS.LIVE badge */
        .sys-live {
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: .55rem 1.25rem .45rem;
            border-bottom: 1px solid var(--sidebar-border);
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
            color: var(--sidebar-text-soft);
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .8rem 1.25rem .2rem;
        }

        /* Nav links */
        #sidebar .nav-link {
            font-family: var(--font-body);
            font-size: .84rem;
            font-weight: 400;
            color: var(--sidebar-text-soft);
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
            color: var(--sidebar-text-soft);
        }
        #sidebar .nav-link:hover {
            background: var(--sidebar-hover-bg);
            color: var(--sidebar-hover-text);
            border-left-color: rgba(20,199,154,.4);
        }
        #sidebar .nav-link:hover i { color: var(--sidebar-hover-text); }
        #sidebar .nav-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            border-left-color: var(--signal);
            font-weight: 600;
        }
        #sidebar .nav-link.active i { color: var(--sidebar-active-text); }

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
            background: var(--sidebar-hover-bg) !important;
            border-color: rgba(20,199,154,.35) !important;
            color: var(--sidebar-hover-text);
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
            left: var(--sidebar-current-width);
            right: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            gap: .75rem;
            transition: left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
            max-width: 420px;
            min-width: 280px;
            transition: max-width 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .topbar-search-container {
            display: flex;
            align-items: center;
        }
        .topbar-search-close {
            background: none;
            border: none;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: color 0.15s;
        }
        .topbar-search-close:hover {
            color: var(--signal) !important;
        }
        .topbar-search-close i {
            font-size: 1.25rem;
            color: var(--text-soft);
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
        html[data-theme="dark"] .topbar-search-close i {
            color: var(--text-soft);
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
        @media (min-width: 576px) {
            .topbar-search:focus-within {
                max-width: 540px;
            }
        }
        @media (min-width: 768px) and (max-width: 991.98px) {
            .topbar-search {
                max-width: 320px;
                min-width: 240px;
            }
            .topbar-search:focus-within {
                max-width: 440px;
            }
        }
        @media (min-width: 576px) and (max-width: 767.98px) {
            .topbar-search {
                max-width: 240px;
                min-width: 200px;
            }
            .topbar-search:focus-within {
                max-width: 320px;
            }
        }

        /* Live Global Search Dropdown */
        .search-kbd-shortcut {
            position: absolute;
            right: .6rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: .65rem;
            padding: .15rem .35rem;
            border-radius: .25rem;
            background: var(--surface);
            color: var(--text-soft);
            border: 1px solid var(--line);
            font-family: var(--font-mono);
            pointer-events: none;
        }

        .search-dropdown-results {
            position: absolute;
            top: calc(100% + .4rem);
            left: 0;
            right: 0;
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: .6rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.15), 0 8px 10px -6px rgba(0,0,0,.1);
            max-height: 420px;
            overflow-y: auto;
            z-index: 1100;
            padding: .35rem 0;
        }

        .search-group-header {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--text-soft);
            padding: .4rem .85rem .2rem;
            display: flex;
            align-items: center;
            gap: .35rem;
            border-top: 1px solid var(--line);
        }

        .search-group-header:first-child {
            border-top: none;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .45rem .85rem;
            text-decoration: none;
            color: var(--text);
            transition: background .12s;
            cursor: pointer;
        }

        .search-result-item:hover,
        .search-result-item.selected {
            background: var(--surface);
            color: var(--signal);
            text-decoration: none;
        }

        .search-result-item .item-title {
            font-size: .83rem;
            font-weight: 600;
            color: var(--text);
            line-height: 1.25;
            transition: color .12s;
        }

        .search-result-item:hover .item-title,
        .search-result-item.selected .item-title {
            color: var(--signal);
        }

        .search-result-item .item-subtitle {
            font-size: .72rem;
            color: var(--text-soft);
            line-height: 1.2;
        }

        .search-result-item .item-badge {
            font-size: .68rem;
            font-weight: 600;
            padding: .15rem .45rem;
            border-radius: .3rem;
            background: rgba(20,199,154,.12);
            color: var(--signal);
            white-space: nowrap;
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
            left: var(--sidebar-current-width);
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
            transition: left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
           MAIN CONTENT & CONTENT WRAPPER FLUIDITY
        ══════════════════════════════════════ */
        #content-wrapper {
            min-width: 0 !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        #main-content {
            margin-left: var(--sidebar-current-width);
            padding-top: calc(var(--topbar-height) + 54px + 1.5rem);
            padding-bottom: 1.5rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), padding 0.25s ease;
            min-width: 0 !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        @media (max-width: 767.98px) {
            #main-content {
                margin-left: 0 !important;
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
        }

        /* ══════════════════════════════════════
           GLOBAL RESPONSIVE CARD SYSTEM
        ══════════════════════════════════════ */
        .card,
        .stat-card,
        .medisense-workspace-card,
        .card-like,
        .messenger-layout {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        .card-header,
        .card-body,
        .card-footer {
            min-width: 0 !important;
            max-width: 100% !important;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        /* Prevent Flexbox / Grid children inside cards from forcing horizontal overflow */
        .card .row,
        .card .d-flex,
        .card [class*="col-"],
        #main-content .row,
        #main-content .d-flex {
            min-width: 0;
        }

        /* Responsive Table & Media Wrappers inside Cards */
        .card .table-responsive,
        .card-body > .table {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .card canvas,
        .card svg:not(.sb-pulse-icon):not(.stat-sparkline) {
            max-width: 100% !important;
            height: auto;
        }

        #page-intro-skeleton {
            margin-left: var(--sidebar-current-width) !important;
            padding-top: calc(var(--topbar-height) + 54px + 1.5rem) !important;
            padding-bottom: 1.5rem !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
            min-height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
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
           MASTER DESKTOP CARD-BODY COMPOSITION ENGINE
           (Zero Internal Reflowing/Stacking)
        ══════════════════════════════════════ */
        .stat-card {
            background: var(--card);
            border-radius: .75rem;
            border: 1px solid var(--line);
            border-left: 3px solid var(--signal);
            padding: clamp(0.75rem, 1.8vw, 1.25rem) !important;
            box-shadow: 0 1px 4px rgba(10,31,28,.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0 !important;
            max-width: 100% !important;
        }
        .stat-card.card-signal { border-left-color: var(--signal); }
        .stat-card.card-coral  { border-left-color: var(--coral);  }
        .stat-card.card-amber  { border-left-color: var(--amber);  }
        .stat-card.card-steel  { border-left-color: var(--steel);  }

        /* Locked Horizontal Card-Body Rows — [A] [B] [C] [D] Horizontal Positioning */
        .stat-card-top,
        .card-body .d-flex,
        .card-body > .d-flex {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 0.4rem !important;
            margin-bottom: .6rem;
        }

        /* Phone Screen (< 768px & 360px–414px Viewports) Horizontal Scroll & Row Locking */
        @media (min-width: 360px) and (max-width: 414px), (max-width: 767.98px) {
            .card-body {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .card-body > .d-flex,
            .card-body .d-flex {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                min-width: max-content;
            }
            .card-body .stat-value,
            .card-body h2,
            .card-body h3 {
                font-size: clamp(0.95rem, 4vw, 1.25rem) !important;
                white-space: nowrap !important;
            }
            .card-body .stat-label,
            .card-body small {
                font-size: clamp(0.6rem, 2.5vw, 0.72rem) !important;
                white-space: nowrap !important;
            }
            .card-body .stat-icon-wrap,
            .card-body .rounded-circle {
                width: clamp(24px, 6vw, 32px) !important;
                height: clamp(24px, 6vw, 32px) !important;
                font-size: clamp(0.7rem, 3vw, 0.95rem) !important;
                flex-shrink: 0 !important;
            }
        }

        .stat-value {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: clamp(1.25rem, 3.2vw, 2.1rem) !important;
            color: var(--text);
            line-height: 1.1;
            white-space: nowrap;
        }
        .stat-label {
            font-family: var(--font-body);
            font-size: clamp(0.68rem, 1.4vw, 0.78rem) !important;
            color: var(--text-soft);
            margin-top: .3rem;
            white-space: nowrap;
        }

        /* Proportional Scaling Icons — Same Desktop Position */
        .stat-icon-wrap,
        .card-body .rounded-circle {
            width: clamp(32px, 4vw, 40px) !important;
            height: clamp(32px, 4vw, 40px) !important;
            border-radius: .5rem;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: clamp(0.9rem, 2vw, 1.15rem) !important;
            flex-shrink: 0 !important;
        }
        .stat-card.card-signal .stat-icon-wrap { background: rgba(20,199,154,.12); color: var(--signal-dark); }
        .stat-card.card-coral  .stat-icon-wrap { background: rgba(232,92,85,.12);  color: var(--coral); }
        .stat-card.card-amber  .stat-icon-wrap { background: rgba(224,160,48,.12); color: var(--amber); }
        .stat-card.card-steel  .stat-icon-wrap { background: rgba(76,126,168,.12); color: var(--steel); }

        /* Sparkline SVG & Canvas Chart Responsiveness — Same Desktop Position */
        .stat-sparkline,
        .card-body canvas,
        .card-body svg:not(.sb-pulse-icon) {
            display: block;
            width: 100% !important;
            max-width: 100% !important;
            height: auto;
        }

        .overflow-x-auto-sm {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .overflow-x-auto-sm::-webkit-scrollbar {
            display: none;
        }

        .stat-sparkline {
            height: 28px;
            margin-top: .5rem;
        }

        /* ══════════════════════════════════════
           UNIFIED ONLINE & OFFLINE SPINNER ENGINE
        ══════════════════════════════════════ */
        @keyframes hims-spin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes hims-spinner-grow {
            0%   { transform: scale(0); opacity: 0; }
            50%  { opacity: 1; }
            100% { transform: scale(1); opacity: 0; }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; }
            50%      { opacity: 0.35; }
        }

        /* Spinner Border — Seamless Online (Bootstrap 5 CDN) & Offline */
        .spinner-border {
            display: inline-block !important;
            width: var(--bs-spinner-width, 1.25rem) !important;
            height: var(--bs-spinner-height, 1.25rem) !important;
            vertical-align: var(--bs-spinner-vertical-align, -0.125em) !important;
            border: var(--bs-spinner-border-width, 0.2em) solid currentColor !important;
            border-right-color: transparent !important;
            border-radius: 50% !important;
            animation: hims-spin var(--bs-spinner-animation-speed, 0.75s) linear infinite !important;
        }
        .spinner-border-sm {
            width: var(--bs-spinner-width, 1rem) !important;
            height: var(--bs-spinner-height, 1rem) !important;
            border-width: var(--bs-spinner-border-width, 0.18em) !important;
        }

        /* Spinner Grow — Seamless Online & Offline */
        .spinner-grow {
            display: inline-block !important;
            width: var(--bs-spinner-width, 1.25rem) !important;
            height: var(--bs-spinner-height, 1.25rem) !important;
            vertical-align: var(--bs-spinner-vertical-align, -0.125em) !important;
            background-color: currentColor !important;
            border-radius: 50% !important;
            opacity: 0;
            animation: hims-spinner-grow var(--bs-spinner-animation-speed, 0.75s) linear infinite !important;
        }
        .spinner-grow-sm {
            width: var(--bs-spinner-width, 1rem) !important;
            height: var(--bs-spinner-height, 1rem) !important;
        }

        /* Explicit Icon Spin Classes */
        .spin, .is-loading, .spinning, .icon-spin, .bi-spin {
            display: inline-block !important;
            animation: hims-spin 1s linear infinite !important;
        }
        .pulse-loading {
            animation: pulse-glow 1.5s ease-in-out infinite !important;
        }

        /* ══════════════════════════════════════
           CARDS & FLUID CARD BODIES
        ══════════════════════════════════════ */
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: .75rem;
            box-shadow: 0 1px 4px rgba(10,31,28,.05);
            min-width: 0 !important;
            max-width: 100% !important;
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
        .card-body {
            padding: 1rem 1.1rem;
            min-width: 0 !important;
            max-width: 100% !important;
        }
        .card-body h2,
        .card-body h3 {
            font-size: clamp(1.3rem, 3.2vw, 1.85rem);
            overflow-wrap: anywhere;
        }

        /* ══════════════════════════════════════
           GLOBAL RESPONSIVE TABLE SYSTEM
        ══════════════════════════════════════ */
        .table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.5rem;
            margin-bottom: 0;
        }

        .table {
            width: 100%;
            margin-bottom: 0;
        }

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
            white-space: nowrap;
        }

        .table th.allow-wrap {
            white-space: normal !important;
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

        /* Responsive Cell Content Wrapping */
        .patient-name,
        .report-name,
        .filename,
        .message-text,
        .cell-wrap,
        .text-wrap-break {
            overflow-wrap: anywhere !important;
            word-break: normal !important;
            max-width: 320px;
        }

        /* Responsive Table Action Buttons */
        .table-actions,
        .table td .btn-group,
        .table td .d-flex {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 0.25rem !important;
            align-items: center !important;
        }

        .table td .btn-group .btn {
            border-radius: 0.375rem !important;
        }

        /* Responsive Filter & Search Bar Wrapping */
        .table-filter-bar,
        .search-filter-wrap,
        .filter-row {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
            width: 100% !important;
            align-items: center !important;
        }

        .table-filter-bar .form-control,
        .table-filter-bar .form-select,
        .search-filter-wrap .form-control,
        .search-filter-wrap .form-select {
            min-width: 140px;
            flex: 1 1 auto;
        }

        /* Responsive Pagination Controls */
        .pagination {
            flex-wrap: wrap !important;
            justify-content: center !important;
            gap: 0.25rem !important;
            margin-bottom: 0 !important;
        }

        /* Responsive Modal Dialogs */
        @media (max-width: 575.98px) {
            .modal-dialog {
                margin: 0.5rem !important;
                max-width: calc(100vw - 1rem) !important;
            }
        }

        .modal-content {
            border-radius: 0.75rem !important;
            border: 1px solid var(--line) !important;
            background-color: var(--card) !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
        }

        .modal-body {
            max-height: calc(100vh - 160px) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            min-width: 0 !important;
        }

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

        /* ======================================
           MODERN SCROLLBARS (global & sidebar)
           ====================================== */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
        }

        /* Webkit scrollbar for global application */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        /* Webkit scrollbar specific to client sidebar navigation */
        .sb-nav-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.12) transparent;
        }
        .sb-nav-container::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .sb-nav-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .sb-nav-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
        }
        .sb-nav-container:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
        }
        .sb-nav-container::-webkit-scrollbar-thumb:hover {
            background: var(--signal);
        }

        /* Floating Tooltip */
        .sb-tooltip {
            position: fixed;
            z-index: 1060;
            background: var(--ink);
            color: #fff;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.76rem;
            font-weight: 500;
            font-family: var(--font-display);
            pointer-events: none;
            opacity: 0;
            transform: translate(-10px, -50%);
            transition: opacity 0.12s ease, transform 0.12s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            border: 1px solid rgba(255,255,255,0.08);
            white-space: nowrap;
        }
        .sb-tooltip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: -4px;
            transform: translateY(-50%) rotate(45deg);
            width: 8px;
            height: 8px;
            background: var(--ink);
            border-left: 1px solid rgba(255,255,255,0.08);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sb-tooltip.show {
            opacity: 1;
            transform: translate(0, -50%);
        }

        /* ══════════════════════════════════════
           RESPONSIVE & MINI SIDEBAR COLLAPSE
        ══════════════════════════════════════ */
        @media (min-width: 768px) {
            /* Under collapsed state: */
            body.sidebar-collapsed #sidebar .sb-brand-text,
            body.sidebar-collapsed #sidebar .sb-nav-label,
            body.sidebar-collapsed #sidebar .sb-group-label,
            body.sidebar-collapsed #sidebar .sb-chevron,
            body.sidebar-collapsed #sidebar .sys-live,
            body.sidebar-collapsed #sidebar .brand-tagline,
            body.sidebar-collapsed #sidebar .sb-badge {
                display: none !important;
            }

            body.sidebar-collapsed #sidebar .nav-link {
                justify-content: center !important;
                padding: 0.6rem 0 !important;
                margin: 2px 8px !important;
                font-size: 0 !important;
            }

            body.sidebar-collapsed #sidebar .nav-link i {
                font-size: 1.25rem !important;
                margin: 0 !important;
            }

            body.sidebar-collapsed #sidebar .sb-group-toggle {
                justify-content: center !important;
                padding: 0.6rem 0 !important;
                margin: 2px 8px !important;
                width: calc(100% - 16px) !important;
            }

            body.sidebar-collapsed #sidebar .collapse {
                display: none !important;
            }

            body.sidebar-collapsed #sidebar .sb-brand-inner {
                flex-direction: column !important;
                gap: 8px !important;
                justify-content: center !important;
            }

            body.sidebar-collapsed #sidebar #desktopSidebarCollapse {
                display: none !important;
            }

            body.sidebar-collapsed #sidebar .sb-brand-icon-wrap,
            body.sidebar-collapsed #sidebar .sb-pulse-icon {
                margin: 0 auto !important;
            }

            body.sidebar-collapsed #sidebar #userMenuBtn {
                justify-content: center !important;
                padding: 0.5rem 0 !important;
                border: none !important;
                margin: 2px 8px !important;
            }

            body.sidebar-collapsed #sidebar #userMenuBtn .text-start,
            body.sidebar-collapsed #sidebar #userMenuBtn .bi-chevron-right {
                display: none !important;
            }

            body.sidebar-collapsed #sidebar .nav-link-logout {
                justify-content: center !important;
                padding: 0.5rem 0 !important;
                margin: 2px 8px !important;
            }

            body.sidebar-collapsed #sidebar .nav-link-logout span {
                display: none !important;
            }

            body.sidebar-collapsed #sidebar .nav-link-logout i {
                margin: 0 !important;
                font-size: 1.25rem !important;
            }
        }

        @media (max-width: 767px) {
            #sidebar {
                transform: translateX(-100%) !important;
                width: var(--sidebar-width) !important;
            }
            #sidebar.show {
                transform: translateX(0) !important;
            }
            #topbar {
                left: 0 !important;
            }
            #page-titlebar {
                left: 0 !important;
            }
            #main-content {
                margin-left: 0 !important;
            }
            #page-intro-skeleton {
                margin-left: 0 !important;
            }
            .topbar-toggle {
                display: flex;
            }
            .topbar-title { display: none; }
        }

        @media (max-width: 575.98px) {
            .topbar-search {
                display: none;
                flex: none;
                max-width: none;
                width: 100%;
            }
            .topbar-search.active {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: var(--topbar-height);
                background: var(--card);
                border-bottom: 1px solid var(--line);
                z-index: 1035;
                padding: 0 1.25rem;
                display: flex !important;
                align-items: center;
            }
            .topbar-search.active input {
                font-size: 0.9rem;
                padding: 0.45rem 0.75rem 0.45rem 2.2rem;
            }
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
    <div class="sb-brand" style="position: relative;">
        <div class="sb-brand-inner d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2" style="min-width: 0; overflow: hidden; flex: 1;">
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
                    <svg class="sb-pulse-icon" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="flex-shrink: 0;">
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
                    <h5 class="m-0">HIMS - {{ $brandSuffix }}</h5>
                    @if($roleTagline)
                        <span class="brand-tagline" style="color: var(--signal); font-size: 0.72rem; font-weight: 600; display: block; margin-top: -1px; margin-bottom: 2px; text-transform: uppercase;">{{ $roleTagline }}</span>
                    @endif
                    <small>Hospital Suite</small>
                </div>
            </div>

            {{-- Desktop Collapse Toggle --}}
            <button class="topbar-toggle d-none d-md-flex align-items-center justify-content-center border-0 p-0" id="desktopSidebarCollapse" aria-label="Collapse sidebar" style="width: 24px; height: 24px; background: transparent; color: var(--sidebar-text-soft); transition: color 0.15s; outline: none;">
                <i class="bi bi-arrow-bar-left" id="desktopCollapseIcon" style="font-size:1.15rem;"></i>
            </button>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="sb-nav-container" style="flex:1; overflow-y:auto; padding-top:.5rem; -webkit-overflow-scrolling: touch;">
        @include('partials._sidebar')
    </div>

    {{-- User Account Menu (Stuck at the bottom) --}}
    <div class="px-3 py-3 border-top" style="border-color: var(--sidebar-border) !important; background: var(--sidebar-bg); z-index: 1050; flex-shrink: 0;">
        <div class="d-flex flex-column gap-1">
            {{-- Direct User Profile Button --}}
            <button type="button" 
               class="nav-link sb-group-toggle w-100 text-start d-flex align-items-center justify-content-between p-2 rounded"
               style="background: transparent; border: 1px solid transparent; text-decoration: none;"
               id="userMenuBtn" 
               data-bs-toggle="modal"
               data-bs-target="#userMenuModal"
               aria-label="User profile">
                <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                    <div class="topbar-avatar" style="width: 30px; height: 30px; border-radius: 50%; background: var(--signal); color: var(--ink); font-family: var(--font-display); font-weight: 700; font-size: .75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="text-start" style="line-height: 1.2; min-width: 0;">
                        <div style="font-size: .8rem; font-weight: 600; color: var(--sidebar-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 145px;">{{ auth()->user()->name }}</div>
                        <div style="font-size: .65rem; color: var(--sidebar-text-soft); font-family: var(--font-mono); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 145px;">{{ auth()->user()->roleName }}</div>
                    </div>
                </div>
                <i class="bi bi-chevron-expand" style="font-size: .75rem; color: var(--sidebar-text-soft);"></i>
            </button>
        </div>
    </div>

</nav>

{{-- ═══════════════════ TOPBAR ═══════════════════ --}}
<header id="topbar">

    {{-- Mobile Hamburger --}}
    <button class="topbar-toggle d-md-none" id="sidebarToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">
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
    <div class="topbar-search" id="topbarSearch">
        <div class="topbar-search-container w-100">
            <button class="topbar-search-close d-sm-none btn border-0 p-0 me-2 text-soft" id="mobileSearchClose" type="button" aria-label="Close search">
                <i class="bi bi-arrow-left"></i>
            </button>
            <div class="topbar-search-wrap flex-fill position-relative">
                <i class="bi bi-search"></i>
                <input type="search" id="topbarSearchInput" placeholder="Search patients, records…" aria-label="Search" autocomplete="off">
                <kbd class="search-kbd-shortcut d-none d-lg-inline-block">Ctrl K</kbd>
                <div id="searchDropdownResults" class="search-dropdown-results d-none"></div>
            </div>
        </div>
    </div>

    {{-- Right spacer to align search perfectly --}}
    <div class="flex-fill d-none d-sm-block"></div>

    {{-- Right actions: Clock, Messages, Notifications --}}
    <div class="d-flex align-items-center gap-2 ms-auto">
        {{-- Live Clock --}}
        <div class="topbar-clock d-none d-sm-flex" aria-label="Current date and time" aria-live="off">
            <div>
                <div class="topbar-clock-date" id="topbar-date"></div>
                <div class="topbar-clock-time" id="topbar-time"></div>
            </div>
        </div>

        {{-- Mobile Search Trigger --}}
        <button class="topbar-notif d-sm-none" id="mobileSearchToggle" aria-label="Open search">
            <i class="bi bi-search" style="font-size:.9rem;"></i>
        </button>

        {{-- Messages Dropdown --}}
        <div class="dropdown">
            <button class="topbar-notif position-relative" id="topbarMessageDropdownToggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Messages">
                <i class="bi bi-chat-left-text" style="font-size:.9rem;"></i>
                <span class="badge bg-danger rounded-circle position-absolute d-none" id="msgBadge" style="top: -2px; right: -2px; font-size: 0.55rem; padding: 2px 4px;"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 mt-2" aria-labelledby="topbarMessageDropdownToggle" style="width: 320px; font-size: 0.85rem;">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
                    <span class="fw-bold text-dark me-2">Staff Messages</span>
                    <a href="{{ route('messages.index') }}" class="text-decoration-none small text-primary fw-semibold">View all →</a>
                </div>
                <div id="topbarMessageList" style="max-height: 280px; overflow-y: auto;">
                    <div class="text-center py-4 text-muted small me-2 ms-2">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading messages...
                    </div>
                </div>
                <div class="p-2 border-top bg-light text-center">
                    <a href="{{ route('messages.index') }}" class="btn btn-sm btn-primary w-100 py-1">Open Messaging Hub</a>
                </div>
            </div>
        </div>

        {{-- Notifications Dropdown --}}
        <div class="dropdown">
            <button class="topbar-notif position-relative" id="topbarNotifDropdownToggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                <i class="bi bi-bell" style="font-size:.9rem;"></i>
                <span class="notif-dot d-none" id="notifDot" aria-hidden="true"></span>
                <span class="badge bg-danger rounded-circle position-absolute d-none" id="notifBadge" style="top: -2px; right: -2px; font-size: 0.55rem; padding: 2px 4px;"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 mt-2" aria-labelledby="topbarNotifDropdownToggle" style="width: 340px; font-size: 0.85rem;">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
                    <div>
                        <span class="fw-bold text-dark me-2">Notifications</span>
                        <span class="badge bg-secondary rounded-pill" id="notifDropdownCount" style="font-size: 0.65rem;">0</span>
                    </div>
                    <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted small" id="markAllReadBtn">
                        Mark all read
                    </button>
                </div>
                <div id="topbarNotifList" style="max-height: 300px; overflow-y: auto;">
                    <div class="text-center py-4 text-muted small me-2 ms-2">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading notifications...
                    </div>
                </div>
                <div class="p-2 border-top bg-light text-center">
                    <a href="{{ route('notifications.index') }}" class="text-decoration-none small fw-bold text-primary">View all notifications →</a>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- ═══ PAGE TITLE BAR ═══ --}}
<div id="page-titlebar">
    @hasSection('page-titlebar-custom')
        @yield('page-titlebar-custom')
    @else
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
    @endif
</div>

{{-- ═══ TOAST MESSAGES ═══ --}}
<div id="toast-container">
    @include('partials._flash')
</div>

{{-- Page Intro Skeleton loader --}}
<div id="page-intro-skeleton">
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
    @if(request()->routeIs('*.dashboard') || request()->routeIs('dashboard') || request()->routeIs('doctor.dashboard'))
    <div class="mb-3" style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:42px;height:42px;border-radius:.65rem;background:rgba(20,199,154,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-person-check" style="font-size:1.15rem;color:var(--signal-dark);"></i>
        </div>
        <div>
            <div style="font-family:var(--font-display);font-weight:700;font-size:.95rem;color:var(--text);line-height:1.3;">
                Welcome, {{ auth()->user()->name }}
            </div>
            <div style="font-size:.78rem;color:var(--text-soft);line-height:1.3;">
                You are logged in to the DITC Hospital
            </div>
        </div>
    </div>
    @endif
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

    /* ── Desktop Sidebar Collapse ── */
    const desktopCollapseBtn = document.getElementById('desktopSidebarCollapse');
    const desktopCollapseIcon = document.getElementById('desktopCollapseIcon');
    
    // Read and apply initial state from localStorage
    const sidebarState = localStorage.getItem('sidebar_collapsed');
    if (sidebarState === 'true') {
        document.body.classList.add('sidebar-collapsed');
        if (desktopCollapseIcon) {
            desktopCollapseIcon.className = 'bi bi-arrow-bar-right';
        }
    }
    
    desktopCollapseBtn?.addEventListener('click', () => {
        const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebar_collapsed', isCollapsed);
        if (desktopCollapseIcon) {
            desktopCollapseIcon.className = isCollapsed ? 'bi bi-arrow-bar-right' : 'bi bi-arrow-bar-left';
        }
        
        // Dispatch window resize event so charts or layouts update
        window.dispatchEvent(new Event('resize'));
    });

    // Auto-expand sidebar if any link or toggle is clicked while collapsed
    document.querySelectorAll('#sidebar .nav-link, #sidebar .sb-group-toggle').forEach(elem => {
        elem.addEventListener('click', (e) => {
            if (elem.id === 'userMenuBtn') return; // Don't auto-expand for user menu modal
            if (document.body.classList.contains('sidebar-collapsed')) {
                document.body.classList.remove('sidebar-collapsed');
                localStorage.setItem('sidebar_collapsed', 'false');
                if (desktopCollapseIcon) {
                    desktopCollapseIcon.className = 'bi bi-arrow-bar-left';
                }
                window.dispatchEvent(new Event('resize'));
            }
        });
    });

    // Expand sidebar when clicking on the brand icon / logo in mini mode
    document.querySelectorAll('.sb-brand-icon-wrap, .sb-pulse-icon').forEach(elem => {
        elem.style.cursor = 'pointer';
        elem.addEventListener('click', () => {
            if (document.body.classList.contains('sidebar-collapsed')) {
                document.body.classList.remove('sidebar-collapsed');
                localStorage.setItem('sidebar_collapsed', 'false');
                if (desktopCollapseIcon) {
                    desktopCollapseIcon.className = 'bi bi-arrow-bar-left';
                }
                window.dispatchEvent(new Event('resize'));
            }
        });
    });

    // Dynamic floating tooltip for collapsed sidebar icons
    let tooltipEl = null;

    document.querySelectorAll('#sidebar .nav-link, #sidebar .sb-group-toggle, #sidebar #userMenuBtn').forEach(elem => {
        elem.addEventListener('mouseenter', (e) => {
            if (!document.body.classList.contains('sidebar-collapsed')) return;
            
            let text = '';
            if (elem.id === 'userMenuBtn') {
                text = 'Account Menu';
            } else if (elem.classList.contains('nav-link-logout')) {
                text = 'Log Out';
            } else {
                const groupLabel = elem.querySelector('.sb-group-label');
                if (groupLabel) {
                    const clone = groupLabel.cloneNode(true);
                    clone.querySelectorAll('.sb-badge').forEach(b => b.remove());
                    text = clone.textContent.trim();
                } else {
                    const clone = elem.cloneNode(true);
                    clone.querySelectorAll('i, svg').forEach(i => i.remove());
                    text = clone.textContent.trim();
                }
            }
            
            if (!text) return;
            
            // Create tooltip
            tooltipEl = document.createElement('div');
            tooltipEl.className = 'sb-tooltip';
            tooltipEl.textContent = text;
            document.body.appendChild(tooltipEl);
            
            // Positioning coordinates
            const rect = elem.getBoundingClientRect();
            tooltipEl.style.top = `${rect.top + (rect.height / 2)}px`;
            tooltipEl.style.left = `${rect.right + 10}px`;
            
            requestAnimationFrame(() => {
                tooltipEl?.classList.add('show');
            });
        });
        
        elem.addEventListener('mouseleave', () => {
            if (tooltipEl) {
                const temp = tooltipEl;
                tooltipEl = null;
                temp.classList.remove('show');
                setTimeout(() => temp.remove(), 120);
            }
        });
        
        elem.addEventListener('click', () => {
            if (tooltipEl) {
                tooltipEl.remove();
                tooltipEl = null;
            }
        });
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

    /* ── Centralized Global Confirmation Modal Interceptor ── */
    (function() {
        let pendingFormOrElement = null;

        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('[data-confirm]');
            if (!trigger) return;

            const confirmMsg = trigger.getAttribute('data-confirm');
            if (!confirmMsg) return;

            const form = trigger.closest('form');

            e.preventDefault();
            e.stopPropagation();

            const modalEl = document.getElementById('globalConfirmModal');
            if (!modalEl) {
                if (confirm(confirmMsg)) {
                    if (form) form.submit();
                    else if (trigger.tagName === 'A') window.location.href = trigger.href;
                }
                return;
            }

            const titleEl = document.getElementById('globalConfirmTitle');
            const msgEl = document.getElementById('globalConfirmMessage');
            const actionBtn = document.getElementById('globalConfirmActionButton');
            const iconBg = document.getElementById('globalConfirmIconBg');
            const iconEl = document.getElementById('globalConfirmIcon');

            const title = trigger.getAttribute('data-confirm-title') || 'Confirm Action';
            const btnClass = trigger.getAttribute('data-confirm-btn') || 'btn-danger';
            const iconClass = trigger.getAttribute('data-confirm-icon') || (btnClass.includes('danger') ? 'bi-exclamation-triangle-fill' : (btnClass.includes('warning') ? 'bi-exclamation-circle-fill' : (btnClass.includes('info') ? 'bi-unlock-fill' : 'bi-question-circle-fill')));
            const actionBtnText = trigger.getAttribute('data-confirm-action-text') || 'Confirm';

            if (titleEl) titleEl.textContent = title;
            if (msgEl) msgEl.textContent = confirmMsg;

            if (actionBtn) {
                actionBtn.className = `btn btn-sm ${btnClass} px-3`;
                actionBtn.innerHTML = `<i class="bi bi-check-lg me-1"></i>${actionBtnText}`;
            }

            if (iconBg && iconEl) {
                iconEl.className = `bi ${iconClass} fs-5`;
                if (btnClass.includes('danger')) {
                    iconBg.style.background = 'rgba(232, 92, 85, 0.15)';
                    iconBg.style.color = 'var(--coral)';
                } else if (btnClass.includes('warning')) {
                    iconBg.style.background = 'rgba(224, 160, 48, 0.15)';
                    iconBg.style.color = 'var(--amber)';
                } else if (btnClass.includes('info') || btnClass.includes('primary') || btnClass.includes('success')) {
                    iconBg.style.background = 'rgba(20, 199, 154, 0.15)';
                    iconBg.style.color = 'var(--signal-dark)';
                } else {
                    iconBg.style.background = 'rgba(76, 126, 168, 0.15)';
                    iconBg.style.color = 'var(--steel)';
                }
            }

            pendingFormOrElement = { form: form, link: trigger.tagName === 'A' ? trigger : null };

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }, true);

        // Confirm button action listener
        document.getElementById('globalConfirmActionButton')?.addEventListener('click', function() {
            const modalEl = document.getElementById('globalConfirmModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            if (pendingFormOrElement) {
                if (pendingFormOrElement.form) {
                    pendingFormOrElement.form.submit();
                } else if (pendingFormOrElement.link) {
                    window.location.href = pendingFormOrElement.link.href;
                }
                pendingFormOrElement = null;
            }
        });
    })();

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

    /* ── Global Live Search & Mobile Overlay Handler ── */
    const mobileSearchToggle = document.getElementById('mobileSearchToggle');
    const mobileSearchClose  = document.getElementById('mobileSearchClose');
    const topbarSearch       = document.getElementById('topbarSearch');
    const searchInput        = document.getElementById('topbarSearchInput');
    const searchDropdown     = document.getElementById('searchDropdownResults');

    let debounceTimer = null;
    let selectedIndex = -1;

    // Mobile Overlay Toggle
    mobileSearchToggle?.addEventListener('click', () => {
        if (topbarSearch) {
            topbarSearch.classList.add('active');
            setTimeout(() => searchInput?.focus(), 50);
        }
    });

    mobileSearchClose?.addEventListener('click', () => {
        if (topbarSearch) {
            topbarSearch.classList.remove('active');
            if (searchInput) searchInput.value = '';
            hideSearchDropdown();
        }
    });

    function hideSearchDropdown() {
        if (searchDropdown) {
            searchDropdown.classList.add('d-none');
            searchDropdown.innerHTML = '';
        }
        selectedIndex = -1;
    }

    function renderSearchDropdown(data) {
        if (!searchDropdown) return;
        
        const results = data.results || {};
        const categories = Object.keys(results);

        if (categories.length === 0) {
            searchDropdown.innerHTML = `
                <div class="px-3 py-3 text-center text-soft" style="font-size: .83rem;">
                    <i class="bi bi-search d-block mb-1 fs-5 opacity-50"></i>
                    No matching records found for "<strong class="text-ink">${escapeHtml(data.query)}</strong>"
                </div>`;
            searchDropdown.classList.remove('d-none');
            selectedIndex = -1;
            return;
        }

        let html = '';
        categories.forEach(catKey => {
            const group = results[catKey];
            html += `
                <div class="search-group-header">
                    <i class="bi ${group.icon}"></i>
                    ${escapeHtml(group.label)}
                </div>`;
            
            group.items.forEach(item => {
                const badgeHtml = item.badge ? `<span class="item-badge">${escapeHtml(item.badge)}</span>` : '';
                html += `
                    <a href="${item.url}" class="search-result-item" data-url="${item.url}">
                        <div>
                            <div class="item-title">${escapeHtml(item.title)}</div>
                            <div class="item-subtitle">${escapeHtml(item.subtitle)}</div>
                        </div>
                        ${badgeHtml}
                    </a>`;
            });
        });

        searchDropdown.innerHTML = html;
        searchDropdown.classList.remove('d-none');
        selectedIndex = -1;

        // Hover & Click Navigation Events
        searchDropdown.querySelectorAll('.search-result-item').forEach((el, idx) => {
            el.addEventListener('mouseenter', () => setSelectedIndex(idx));

            const navigateToResult = (e) => {
                const url = el.getAttribute('data-url');
                if (url && url !== '#') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.location.href = url;
                }
            };

            el.addEventListener('mousedown', navigateToResult);
            el.addEventListener('click', navigateToResult);
        });
    }

    function setSelectedIndex(index) {
        const items = searchDropdown?.querySelectorAll('.search-result-item') || [];
        items.forEach(i => i.classList.remove('selected'));
        if (index >= 0 && index < items.length) {
            items[index].classList.add('selected');
            items[index].scrollIntoView({ block: 'nearest' });
            selectedIndex = index;
        } else {
            selectedIndex = -1;
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Input Debounce Live Search
    searchInput?.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            hideSearchDropdown();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/global-search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => renderSearchDropdown(data))
            .catch(err => console.error('Global search error:', err));
        }, 220);
    });

    // Keyboard Navigation & Shortcuts
    document.addEventListener('keydown', (e) => {
        const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        const isInputActive = ['input', 'textarea', 'select'].includes(activeTag) || document.activeElement?.isContentEditable;

        // Global hotkey Ctrl+K or '/' to focus search
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            searchInput?.focus();
            return;
        }
        if (e.key === '/' && !isInputActive) {
            e.preventDefault();
            searchInput?.focus();
            return;
        }

        // Dropdown active key navigation
        if (searchDropdown && !searchDropdown.classList.contains('d-none')) {
            const items = searchDropdown.querySelectorAll('.search-result-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIndex = selectedIndex + 1 < items.length ? selectedIndex + 1 : 0;
                setSelectedIndex(nextIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevIndex = selectedIndex - 1 >= 0 ? selectedIndex - 1 : items.length - 1;
                setSelectedIndex(prevIndex);
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    e.preventDefault();
                    window.location.href = items[selectedIndex].getAttribute('data-url');
                } else if (searchInput?.value.trim()) {
                    e.preventDefault();
                    window.location.href = `/patients?search=${encodeURIComponent(searchInput.value.trim())}`;
                }
            } else if (e.key === 'Escape') {
                hideSearchDropdown();
                if (topbarSearch?.classList.contains('active')) {
                    topbarSearch.classList.remove('active');
                }
            }
        } else if (e.key === 'Escape' && topbarSearch?.classList.contains('active')) {
            topbarSearch.classList.remove('active');
        }
    });

    // Hide dropdown on click outside
    document.addEventListener('click', (e) => {
        if (topbarSearch && !topbarSearch.contains(e.target)) {
            hideSearchDropdown();
        }
    });
</script>

{{-- Skeleton Loading System --}}
<script src="{{ asset('js/skeleton.js') }}"></script>

{{-- Page Intro Loader Logic --}}
<script>
    (function() {
        window.__pageStartTime = performance.now();
    })();

    document.addEventListener('DOMContentLoaded', function () {
        const intro = document.getElementById('page-intro-skeleton');
        const main = document.getElementById('main-content');
        if (intro && main) {
            const navEntries = performance.getEntriesByType('navigation');
            const navType = navEntries.length > 0 ? navEntries[0].type : '';
            const isRefreshOrBackForward = (navType === 'reload' || navType === 'back_forward');

            if (isRefreshOrBackForward) {
                // Skeleton loader displays for Page Refresh and Browser Back/Forward navigation
                const SKELETON_MIN_DISPLAY_MS = 400;
                const elapsed = performance.now() - (window.__pageStartTime || performance.now());
                const remaining = Math.max(0, SKELETON_MIN_DISPLAY_MS - elapsed);

                setTimeout(function() {
                    intro.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    intro.style.opacity = '0';
                    intro.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        intro.remove();
                        main.classList.remove('d-none');
                        main.style.animation = 'sk-fade-in 0.3s ease-out forwards';
                    }, 250);
                }, remaining);
            } else {
                // Standard link navigation: reveal main content immediately
                intro.remove();
                main.classList.remove('d-none');
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
            document.documentElement.setAttribute('data-bs-theme', actualTheme);
            
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
            const overlay = document.getElementById('cardio-loader-overlay');
            if (overlay) {
                overlay.style.display = 'flex';
            }
            setTimeout(() => { btn.disabled = true; }, 10);
        }
    });

    // Intercept navigation links (sidebar nav-links, action buttons, table links) to show loading state
    document.addEventListener('click', function (e) {
        if (e.defaultPrevented || e.button !== 0 || e.altKey || e.ctrlKey || e.metaKey || e.shiftKey) return;

        const link = e.target.closest('a');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:') || link.hasAttribute('data-bs-toggle') || link.getAttribute('target') === '_blank' || link.hasAttribute('download')) {
            return;
        }

        try {
            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) return;
        } catch (_) {
            return;
        }

        const overlay = document.getElementById('cardio-loader-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    });

    // Handle Back-Forward Cache (bfcache) navigation
    window.addEventListener('pageshow', function (event) {
        const overlay = document.getElementById('cardio-loader-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
        document.querySelectorAll('button[type="submit"]:disabled, input[type="submit"]:disabled').forEach(btn => {
            btn.disabled = false;
        });
        if (event.persisted) {
            const intro = document.getElementById('page-intro-skeleton');
            const main = document.getElementById('main-content');
            if (main && main.classList.contains('d-none')) {
                if (intro) intro.remove();
                main.classList.remove('d-none');
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

{{-- User Menu Modal --}}
<div class="modal fade" id="userMenuModal" tabindex="-1" aria-labelledby="userMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border border-line" style="border-radius: 1rem; background: var(--card); color: var(--text); box-shadow: 0 15px 40px rgba(0,0,0,0.2);">
            <div class="modal-body p-3">
                <div class="d-flex flex-column gap-1">
                    {{-- Profile --}}
                    <a href="{{ route('profile.edit') }}" class="btn btn-link text-start text-decoration-none py-2 px-3 rounded d-flex align-items-center gap-2 modal-menu-link" style="color: var(--text);">
                        <i class="bi bi-person text-secondary" style="font-size: 1.1rem;"></i>
                        <span style="font-size: 0.88rem; font-weight: 500;">Profile</span>
                    </a>

                    {{-- Settings --}}
                    <a href="{{ route('settings.index') }}" class="btn btn-link text-start text-decoration-none py-2 px-3 rounded d-flex align-items-center gap-2 modal-menu-link" style="color: var(--text);">
                        <i class="bi bi-gear text-secondary" style="font-size: 1.1rem;"></i>
                        <span style="font-size: 0.88rem; font-weight: 500;">Settings</span>
                    </a>

                    <hr class="my-2" style="border-top: 1px solid var(--line); opacity: 1; margin-left:-1rem; margin-right:-1rem;">

                    {{-- Appearance & Theme Options --}}
                    <div class="px-3 py-1">
                        <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.65rem; font-family: var(--font-mono); letter-spacing: 0.05em;">Appearance</div>
                        <div class="d-flex flex-column gap-1 w-100">
                            <button type="button" class="btn btn-sm border-0 theme-btn text-start py-2 px-2.5 d-flex align-items-center gap-2 rounded w-100" data-theme-val="light" style="font-size: 0.82rem; transition: all 0.2s;">
                                <i class="bi bi-sun"></i>
                                <span>Light</span>
                            </button>
                            <button type="button" class="btn btn-sm border-0 theme-btn text-start py-2 px-2.5 d-flex align-items-center gap-2 rounded w-100" data-theme-val="dark" style="font-size: 0.82rem; transition: all 0.2s;">
                                <i class="bi bi-moon-stars"></i>
                                <span>Dark</span>
                            </button>
                            <button type="button" class="btn btn-sm border-0 theme-btn text-start py-2 px-2.5 d-flex align-items-center gap-2 rounded w-100" data-theme-val="system" style="font-size: 0.82rem; transition: all 0.2s;">
                                <i class="bi bi-display"></i>
                                <span>System</span>
                            </button>
                        </div>
                    </div>

                    <hr class="my-2" style="border-top: 1px solid var(--line); opacity: 1; margin-left:-1rem; margin-right:-1rem;">

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-link text-start text-decoration-none py-2 px-3 rounded d-flex align-items-center gap-2 w-100 modal-menu-logout" style="color: var(--coral);">
                            <i class="bi bi-box-arrow-right" style="font-size: 1.1rem; color: var(--coral);"></i>
                            <span style="font-size: 0.88rem; font-weight: 600;">Log Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Global Confirmation Modal --}}
<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; background: var(--card); color: var(--text);">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div id="globalConfirmIconBg" class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(232, 92, 85, 0.12); color: var(--coral);">
                        <i id="globalConfirmIcon" class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0" id="globalConfirmTitle" style="font-family: var(--font-display); font-size: 1.1rem; color: var(--text);">Confirm Action</h5>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3 px-4" id="globalConfirmMessage" style="font-size: 0.9rem; color: var(--text-soft); line-height: 1.5;">
                Are you sure you want to proceed with this action?
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4 d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" id="globalConfirmCancelBtn" style="border-radius: 0.5rem; font-weight: 500;">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger px-3" id="globalConfirmActionButton" style="border-radius: 0.5rem; font-weight: 600;">
                    <i class="bi bi-check-lg me-1"></i>Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling & Position handling for userMenuModal */
    #userMenuModal .modal-dialog {
        position: fixed;
        margin: 0;
        z-index: 1060;
        transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.18s ease-out !important;
    }

    /* Normal (Expanded) state: Inside sidebar, above profile button */
    @media (min-width: 768px) {
        #userMenuModal .modal-dialog {
            bottom: 84px;
            left: 12px;
            width: 236px;
            max-width: none;
        }
    }

    /* Collapsed (Mini) state: Next to sidebar, beside avatar button */
    @media (min-width: 768px) {
        body.sidebar-collapsed #userMenuModal .modal-dialog {
            bottom: 12px;
            left: 80px;
            width: 260px;
            max-width: none;
        }
    }

    /* Mobile state: anchored centered at standard size */
    @media (max-width: 767px) {
        #userMenuModal .modal-dialog {
            bottom: 20px;
            left: 12px;
            right: 12px;
            width: calc(100% - 24px);
            max-width: none;
        }
    }

    /* Transition classes */
    #userMenuModal.fade .modal-dialog {
        transform: scale(0.96) translateY(8px) !important;
        opacity: 0;
    }
    #userMenuModal.show .modal-dialog {
        transform: scale(1) translateY(0) !important;
        opacity: 1;
    }

    /* Hover backgrounds & interactions */
    .modal-menu-link {
        transition: all 0.2s ease;
    }
    .modal-menu-link:hover {
        background-color: rgba(20, 199, 154, 0.08) !important;
        color: var(--signal-dark) !important;
    }
    .modal-menu-link:hover i {
        color: var(--signal-dark) !important;
    }
    .modal-menu-logout {
        transition: all 0.2s ease;
    }
    .modal-menu-logout:hover {
        background-color: rgba(232, 92, 85, 0.08) !important;
        color: var(--coral) !important;
    }
    html[data-theme="dark"] .modal-menu-link:hover {
        background-color: rgba(20, 199, 154, 0.15) !important;
        color: var(--signal) !important;
    }
    html[data-theme="dark"] .modal-menu-link:hover i {
        color: var(--signal) !important;
    }
    html[data-theme="dark"] .modal-menu-logout:hover {
        background-color: rgba(232, 92, 85, 0.15) !important;
        color: #ff766f !important;
    }

    /* Specific overrides for modal theme buttons */
    #userMenuModal .theme-btn {
        background-color: transparent !important;
        color: var(--text-soft) !important;
        border: 1px solid transparent !important;
    }
    #userMenuModal .theme-btn:hover {
        background-color: rgba(110, 124, 116, 0.05) !important;
        color: var(--text) !important;
    }
    #userMenuModal .theme-btn.active {
        background-color: rgba(20, 199, 154, 0.08) !important;
        color: var(--signal-dark) !important;
        font-weight: 600;
    }
    html[data-theme="dark"] #userMenuModal .theme-btn.active {
        background-color: rgba(20, 199, 154, 0.15) !important;
        color: var(--signal) !important;
        font-weight: 600;
    }
</style>

<script>
    // Global Confirmation Modal Engine
    (function() {
        let pendingConfirmCallback = null;
        let pendingConfirmForm = null;
        let pendingConfirmLink = null;
        let isSubmitting = false;

        window.showConfirmModal = window.confirmModal = function(options) {
            options = options || {};
            const modalEl = document.getElementById('globalConfirmModal');
            if (!modalEl) return;

            const titleEl = document.getElementById('globalConfirmTitle');
            const messageEl = document.getElementById('globalConfirmMessage');
            const iconBgEl = document.getElementById('globalConfirmIconBg');
            const iconEl = document.getElementById('globalConfirmIcon');
            const actionBtn = document.getElementById('globalConfirmActionButton');

            if (titleEl) titleEl.textContent = options.title || 'Confirm Action';
            if (messageEl) messageEl.innerHTML = options.message || options.body || 'Are you sure you want to proceed with this action?';

            if (actionBtn) {
                const btnClass = options.btnClass || 'btn-danger';
                const actionText = options.actionText || options.btnText || 'Confirm';
                const iconClass = options.icon || options.iconClass || 'bi-check-lg';

                actionBtn.className = `btn btn-sm px-3 ${btnClass}`;
                actionBtn.style.borderRadius = '0.5rem';
                actionBtn.style.fontWeight = '600';
                actionBtn.innerHTML = `<i class="${iconClass} me-1"></i>${actionText}`;
            }

            if (iconEl && iconBgEl) {
                const iconClass = options.icon || options.iconClass || 'bi-exclamation-triangle-fill';
                iconEl.className = `bi ${iconClass} fs-5`;

                if ((options.btnClass || '').includes('btn-info') || (options.btnClass || '').includes('btn-primary')) {
                    iconBgEl.style.background = 'rgba(76, 126, 168, 0.12)';
                    iconBgEl.style.color = 'var(--steel)';
                } else if ((options.btnClass || '').includes('btn-success')) {
                    iconBgEl.style.background = 'rgba(20, 199, 154, 0.12)';
                    iconBgEl.style.color = 'var(--signal)';
                } else if ((options.btnClass || '').includes('btn-warning')) {
                    iconBgEl.style.background = 'rgba(224, 160, 48, 0.12)';
                    iconBgEl.style.color = 'var(--amber)';
                } else {
                    iconBgEl.style.background = 'rgba(232, 92, 85, 0.12)';
                    iconBgEl.style.color = 'var(--coral)';
                }
            }

            pendingConfirmCallback = typeof options.onConfirm === 'function' ? options.onConfirm : null;
            pendingConfirmForm = options.form || null;
            pendingConfirmLink = options.link || null;
            isSubmitting = false;

            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        };

        document.addEventListener('DOMContentLoaded', function() {
            const actionBtn = document.getElementById('globalConfirmActionButton');
            const modalEl = document.getElementById('globalConfirmModal');

            if (actionBtn && modalEl) {
                actionBtn.addEventListener('click', function() {
                    if (isSubmitting) return;
                    isSubmitting = true;

                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    if (pendingConfirmCallback) {
                        const cb = pendingConfirmCallback;
                        pendingConfirmCallback = null;
                        cb();
                    } else if (pendingConfirmForm) {
                        const form = pendingConfirmForm;
                        pendingConfirmForm = null;
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                    } else if (pendingConfirmLink) {
                        const link = pendingConfirmLink;
                        pendingConfirmLink = null;
                        window.location.href = link;
                    }
                });
            }

            // Automatic interception for elements with [data-confirm]
            document.addEventListener('click', function(e) {
                const confirmTarget = e.target.closest('[data-confirm]');
                if (!confirmTarget) return;

                // Prevent immediate form submission or navigation
                e.preventDefault();
                e.stopPropagation();

                const message = confirmTarget.getAttribute('data-confirm');
                const title = confirmTarget.getAttribute('data-confirm-title') || 'Confirm Action';
                const btnClass = confirmTarget.getAttribute('data-confirm-btn') || 'btn-danger';
                const icon = confirmTarget.getAttribute('data-confirm-icon') || 'bi-exclamation-triangle-fill';
                const actionText = confirmTarget.getAttribute('data-confirm-action-text') || 'Confirm';

                const form = confirmTarget.closest('form');
                const href = confirmTarget.getAttribute('href');

                window.showConfirmModal({
                    title: title,
                    message: message,
                    btnClass: btnClass,
                    icon: icon,
                    actionText: actionText,
                    onConfirm: function() {
                        if (confirmTarget.tagName === 'BUTTON' && confirmTarget.type === 'submit' && form) {
                            if (confirmTarget.name) {
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = confirmTarget.name;
                                hidden.value = confirmTarget.value;
                                form.appendChild(hidden);
                            }
                            form.submit();
                        } else if (confirmTarget.tagName === 'A' && href && href !== '#') {
                            window.location.href = href;
                        } else if (form) {
                            form.submit();
                        }
                    }
                });
            }, true);
        });
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const userMenuEl = document.getElementById('userMenuModal');
        if (userMenuEl) {
            userMenuEl.addEventListener('show.bs.modal', function () {
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(el => {
                        el.style.backgroundColor = 'transparent';
                        el.style.opacity = '0';
                    });
                }, 5);
            });
        }
    });

    // Topbar Notifications & Internal Messaging Integration
    document.addEventListener('DOMContentLoaded', function() {
        function loadTopbarCounts() {
            // Fetch Notification Count
            fetch("{{ route('notifications.unread-count') }}")
                .then(res => res.ok ? res.json() : null)
                .then(data => {
                    if (!data) return;
                    const dot = document.getElementById('notifDot');
                    const badge = document.getElementById('notifBadge');
                    const countSpan = document.getElementById('notifDropdownCount');
                    if (data.unread_count > 0) {
                        if (dot) dot.classList.remove('d-none');
                        if (badge) {
                            badge.classList.remove('d-none');
                            badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        }
                        if (countSpan) countSpan.textContent = data.unread_count;
                    } else {
                        if (dot) dot.classList.add('d-none');
                        if (badge) badge.classList.add('d-none');
                        if (countSpan) countSpan.textContent = '0';
                    }
                })
                .catch(() => {});

            // Fetch Message Count
            fetch("{{ route('messages.recent') }}")
                .then(res => res.ok ? res.json() : null)
                .then(data => {
                    if (!data) return;
                    const badge = document.getElementById('msgBadge');
                    if (data.unread_count > 0) {
                        if (badge) {
                            badge.classList.remove('d-none');
                            badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        }
                    } else {
                        if (badge) badge.classList.add('d-none');
                    }
                })
                .catch(() => {});
        }

        // Populate Notification List when dropdown toggled
        const notifBtn = document.getElementById('topbarNotifDropdownToggle');
        if (notifBtn) {
            notifBtn.addEventListener('show.bs.dropdown', function() {
                const list = document.getElementById('topbarNotifList');
                fetch("{{ route('notifications.recent') }}")
                    .then(res => res.ok ? res.json() : null)
                    .then(data => {
                        if (!data || !data.notifications || data.notifications.length === 0) {
                            list.innerHTML = `<div class="text-center py-4 text-muted small"><i class="bi bi-bell-slash d-block mb-1 fs-5 opacity-50"></i>No new notifications</div>`;
                            return;
                        }

                        let html = '';
                        data.notifications.forEach(item => {
                            const bgClass = item.is_read ? 'bg-card text-body' : 'bg-body-tertiary text-body fw-bold';
                            const dotClass = item.priority === 'critical' ? 'bg-danger' : (item.priority === 'urgent' ? 'bg-warning' : 'bg-primary');
                            html += `
                                <a href="${item.target_url}" class="d-flex align-items-start gap-2 p-3 text-decoration-none border-bottom text-body ${bgClass} hover-bg-light">
                                    <span class="rounded-circle mt-1 flex-shrink-0 ${dotClass}" style="width: 8px; height: 8px;"></span>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-secondary" style="font-size: 0.6rem;">${item.module}</span>
                                            <small class="text-body-secondary" style="font-size: 0.65rem;">${item.created_at}</small>
                                        </div>
                                        <div class="small mb-1 text-truncate text-body">${item.title}</div>
                                        <div class="text-body-secondary text-truncate" style="font-size: 0.72rem; font-weight: normal;">${item.message}</div>
                                    </div>
                                </a>
                            `;
                        });
                        list.innerHTML = html;
                    });
            });
        }

        // Populate Message List when dropdown toggled
        const msgBtn = document.getElementById('topbarMessageDropdownToggle');
        if (msgBtn) {
            msgBtn.addEventListener('show.bs.dropdown', function() {
                const list = document.getElementById('topbarMessageList');
                fetch("{{ route('messages.recent') }}")
                    .then(res => res.ok ? res.json() : null)
                    .then(data => {
                        if (!data || !data.conversations || data.conversations.length === 0) {
                            list.innerHTML = `<div class="text-center py-4 text-body-secondary small"><i class="bi bi-chat-dots d-block mb-1 fs-5 opacity-50"></i>No recent staff messages</div>`;
                            return;
                        }

                        let html = '';
                        data.conversations.forEach(item => {
                            const bgClass = item.is_unread ? 'bg-body-tertiary text-body fw-bold' : 'bg-card text-body';
                            html += `
                                <a href="{{ route('messages.index') }}?conversation_id=${item.id}" class="d-flex align-items-center gap-2 p-3 text-decoration-none border-bottom text-body ${bgClass} hover-bg-light">
                                    <div class="topbar-avatar flex-shrink-0" style="width: 32px; height: 32px; border-radius: 50%; background: var(--signal); color: var(--ink); font-weight: 700; font-size: .75rem; display: flex; align-items: center; justify-content: center;">
                                        ${item.other_user_name.charAt(0).toUpperCase()}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small text-truncate text-body">${item.other_user_name}</span>
                                            <small class="text-body-secondary" style="font-size: 0.65rem;">${item.created_at}</small>
                                        </div>
                                        <div class="text-body-secondary text-truncate" style="font-size: 0.72rem; font-weight: normal;">${item.last_message}</div>
                                    </div>
                                </a>
                            `;
                        });
                        list.innerHTML = html;
                    });
            });
        }

        // Mark All Read Button
        const markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetch("{{ route('notifications.mark-all-read') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(() => {
                    loadTopbarCounts();
                    if (notifBtn) notifBtn.dispatchEvent(new Event('show.bs.dropdown'));
                });
            });
        }

        // Initial load & periodic poll every 30s
        loadTopbarCounts();
        setInterval(loadTopbarCounts, 30000);
    });
</script>

@auth
    @unless(request()->routeIs('medisense.index'))
        @include('partials._medisense_fab')
    @endunless

    {{-- Monochromatic Session Ended Warning Modal --}}
    <div id="sessionEndedModal" class="session-ended-overlay d-none">
        <div class="session-ended-dialog">
            <div class="session-ended-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h5 class="session-ended-title">Session Ended</h5>
            <p class="session-ended-text">Your account was signed in on another device. You have been logged out for security.</p>
            <div class="session-ended-countdown-wrap">
                <span>Redirecting in <strong id="sessionEndedCountdown">5</strong>s</span>
                <div class="session-ended-progress">
                    <div id="sessionEndedProgressBar" class="session-ended-progress-bar"></div>
                </div>
            </div>
            <button id="sessionEndedLoginBtn" class="session-ended-btn" type="button">
                <i class="bi bi-box-arrow-in-right me-1"></i> Log In Now
            </button>
        </div>
    </div>

    <style>
        .session-ended-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(10, 15, 13, 0.82);
            backdrop-filter: blur(5px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            animation: fadeInSessionModal 0.2s ease-out;
        }
        @keyframes fadeInSessionModal {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }
        .session-ended-dialog {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            max-width: 400px;
            width: 100%;
            padding: 1.75rem;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
        html[data-theme="dark"] .session-ended-dialog {
            background: #111c19;
            color: #f8fafc;
            border-color: #1e3630;
        }
        .session-ended-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1.25rem;
            border: 1px solid #e2e8f0;
        }
        html[data-theme="dark"] .session-ended-icon {
            background: #192b26;
            color: #cbd5e1;
            border-color: #27453d;
        }
        .session-ended-title {
            font-family: var(--font-display, 'Space Grotesk', sans-serif);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: inherit;
        }
        .session-ended-text {
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1.25rem;
        }
        html[data-theme="dark"] .session-ended-text {
            color: #94a3b8;
        }
        .session-ended-countdown-wrap {
            font-size: 0.82rem;
            font-family: var(--font-mono, monospace);
            color: #475569;
            margin-bottom: 1.25rem;
            background: #f8fafc;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        html[data-theme="dark"] .session-ended-countdown-wrap {
            background: #0b1412;
            color: #cbd5e1;
            border-color: #1e3630;
        }
        .session-ended-progress {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        html[data-theme="dark"] .session-ended-progress {
            background: #1e3630;
        }
        .session-ended-progress-bar {
            height: 100%;
            width: 100%;
            background: #334155;
            transition: width 1s linear;
        }
        html[data-theme="dark"] .session-ended-progress-bar {
            background: #94a3b8;
        }
        .session-ended-btn {
            width: 100%;
            padding: 0.65rem 1rem;
            font-weight: 600;
            font-size: 0.88rem;
            border-radius: 8px;
            border: 1px solid #0f172a;
            background: #0f172a;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .session-ended-btn:hover {
            background: #1e293b;
            border-color: #1e293b;
            color: #ffffff;
        }
        html[data-theme="dark"] .session-ended-btn {
            background: #f8fafc;
            color: #0f172a;
            border-color: #f8fafc;
        }
        html[data-theme="dark"] .session-ended-btn:hover {
            background: #e2e8f0;
            border-color: #e2e8f0;
        }
    </style>

    {{-- Real-time Single Active Session Replacement Listener --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userId = Number("{{ auth()->id() }}");

            function triggerSessionEndedModal() {
                const modal = document.getElementById('sessionEndedModal');
                const targetUrl = "{{ route('login') }}?session_replaced=1";

                if (!modal) {
                    window.location.href = targetUrl;
                    return;
                }

                modal.classList.remove('d-none');
                document.body.style.overflow = 'hidden';

                let seconds = 5;
                const countdownEl = document.getElementById('sessionEndedCountdown');
                const progressBar = document.getElementById('sessionEndedProgressBar');
                const loginBtn = document.getElementById('sessionEndedLoginBtn');

                function goToLogin() {
                    window.location.href = targetUrl;
                }

                if (loginBtn) {
                    loginBtn.addEventListener('click', goToLogin);
                }

                const interval = setInterval(function () {
                    seconds--;
                    if (countdownEl) {
                        countdownEl.textContent = seconds;
                    }
                    if (progressBar) {
                        progressBar.style.width = ((seconds / 5) * 100) + '%';
                    }

                    if (seconds <= 0) {
                        clearInterval(interval);
                        goToLogin();
                    }
                }, 1000);
            }

            if (userId && typeof window.Echo !== 'undefined') {
                window.Echo.private('user.' + userId)
                    .listen('.SessionReplaced', function () {
                        triggerSessionEndedModal();
                    });
            }
        });
    </script>
@endauth

@stack('scripts')
</body>
</html>
