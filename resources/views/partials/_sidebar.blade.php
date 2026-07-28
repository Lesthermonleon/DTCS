{{-- ─── Sidebar Navigation (role-aware) ─── --}}
@php
    $role = auth()->user()->primaryRole;

    // Is any child route in this module currently active?
    $labActive   = request()->routeIs('lab.*');
    $radActive   = request()->routeIs('radiology.*');
    $pmsActive   = request()->routeIs('pharmacy.*');
    $sorActive   = request()->routeIs('surgery.*');
    $dnmActive   = request()->routeIs('diet.*');
    $adminActive = request()->routeIs('admin.*');
@endphp


@if($role === 'admin')
{{-- ──────────────────────────────
     ADMIN: collapsible dropdowns
────────────────────────────── --}}

<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>

<div class="sb-nav-label" style="margin-top:.5rem;">Modules</div>

{{-- ── Laboratory (LIS) ── --}}
<button class="nav-link sb-group-toggle {{ $labActive ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#grp-lab"
        aria-expanded="{{ $labActive ? 'true' : 'false' }}" aria-controls="grp-lab">
    <i class="bi bi-clipboard2-pulse"></i>
    <span class="sb-group-label">Laboratory <span class="sb-badge">LIS</span></span>
    <i class="bi bi-chevron-down sb-chevron"></i>
</button>
<div class="collapse {{ $labActive ? 'show' : '' }}" id="grp-lab">
    <div class="sb-group-body">
        <a href="{{ route('lab.dashboard') }}" class="nav-link sb-sub {{ request()->routeIs('lab.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Overview
        </a>
        <a href="{{ route('lab.requests.index') }}" class="nav-link sb-sub {{ request()->routeIs('lab.requests.*') ? 'active' : '' }}">
            <i class="bi bi-list-task"></i> Lab Requests
        </a>
        <a href="{{ route('lab.results.index') }}" class="nav-link sb-sub {{ request()->routeIs('lab.results.*') ? 'active' : '' }}">
            <i class="bi bi-journal-medical"></i> Lab Results
        </a>
    </div>
</div>

{{-- ── Radiology (RIS) ── --}}
<button class="nav-link sb-group-toggle {{ $radActive ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#grp-rad"
        aria-expanded="{{ $radActive ? 'true' : 'false' }}" aria-controls="grp-rad">
    <i class="bi bi-activity"></i>
    <span class="sb-group-label">Radiology <span class="sb-badge">RIS</span></span>
    <i class="bi bi-chevron-down sb-chevron"></i>
</button>
<div class="collapse {{ $radActive ? 'show' : '' }}" id="grp-rad">
    <div class="sb-group-body">
        <a href="{{ route('radiology.dashboard') }}" class="nav-link sb-sub {{ request()->routeIs('radiology.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Overview
        </a>
        <a href="{{ route('radiology.requests.index') }}" class="nav-link sb-sub {{ request()->routeIs('radiology.requests.*') ? 'active' : '' }}">
            <i class="bi bi-image"></i> Imaging Requests
        </a>
        <a href="{{ route('radiology.reports.index') }}" class="nav-link sb-sub {{ request()->routeIs('radiology.reports.*') ? 'active' : '' }}">
            <i class="bi bi-file-text"></i> Reports
        </a>
    </div>
</div>

{{-- ── Pharmacy (PMS) ── --}}
<button class="nav-link sb-group-toggle {{ $pmsActive ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#grp-pms"
        aria-expanded="{{ $pmsActive ? 'true' : 'false' }}" aria-controls="grp-pms">
    <i class="bi bi-capsule"></i>
    <span class="sb-group-label">Pharmacy <span class="sb-badge">PMS</span></span>
    <i class="bi bi-chevron-down sb-chevron"></i>
</button>
<div class="collapse {{ $pmsActive ? 'show' : '' }}" id="grp-pms">
    <div class="sb-group-body">
        <a href="{{ route('pharmacy.dashboard') }}" class="nav-link sb-sub {{ request()->routeIs('pharmacy.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Overview
        </a>
        <a href="{{ route('pharmacy.prescriptions.index') }}" class="nav-link sb-sub {{ request()->routeIs('pharmacy.prescriptions.*') ? 'active' : '' }}">
            <i class="bi bi-prescription2"></i> Prescriptions
        </a>
        <a href="{{ route('pharmacy.dispensing.index') }}" class="nav-link sb-sub {{ request()->routeIs('pharmacy.dispensing.*') ? 'active' : '' }}">
            <i class="bi bi-bag-plus"></i> Dispensing
        </a>
    </div>
</div>

{{-- ── Surgery (SORS) ── --}}
<button class="nav-link sb-group-toggle {{ $sorActive ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#grp-sor"
        aria-expanded="{{ $sorActive ? 'true' : 'false' }}" aria-controls="grp-sor">
    <i class="bi bi-heart-pulse"></i>
    <span class="sb-group-label">Surgery <span class="sb-badge">SORS</span></span>
    <i class="bi bi-chevron-down sb-chevron"></i>
</button>
<div class="collapse {{ $sorActive ? 'show' : '' }}" id="grp-sor">
    <div class="sb-group-body">
        <a href="{{ route('surgery.dashboard') }}" class="nav-link sb-sub {{ request()->routeIs('surgery.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Overview
        </a>
        <a href="{{ route('surgery.requests.index') }}" class="nav-link sb-sub {{ request()->routeIs('surgery.requests.*') ? 'active' : '' }}">
            <i class="bi bi-scissors"></i> Surgery Requests
        </a>
        <a href="{{ route('surgery.schedules.index') }}" class="nav-link sb-sub {{ request()->routeIs('surgery.schedules.*') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> OR Schedules
        </a>
        <a href="{{ route('surgery.calendar') }}" class="nav-link sb-sub {{ request()->routeIs('surgery.calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar-week"></i> Surgery Calendar
        </a>
    </div>
</div>

{{-- ── Nutrition / Diet (DNMS) ── --}}
<button class="nav-link sb-group-toggle {{ $dnmActive ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#grp-dnm"
        aria-expanded="{{ $dnmActive ? 'true' : 'false' }}" aria-controls="grp-dnm">
    <i class="bi bi-apple"></i>
    <span class="sb-group-label">Nutrition <span class="sb-badge">DNMS</span></span>
    <i class="bi bi-chevron-down sb-chevron"></i>
</button>
<div class="collapse {{ $dnmActive ? 'show' : '' }}" id="grp-dnm">
    <div class="sb-group-body">
        <a href="{{ route('diet.dashboard') }}" class="nav-link sb-sub {{ request()->routeIs('diet.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Overview
        </a>
        <a href="{{ route('diet.requests.index') }}" class="nav-link sb-sub {{ request()->routeIs('diet.requests.*') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark"></i> Diet Requests
        </a>
        <a href="{{ route('diet.plans.index') }}" class="nav-link sb-sub {{ request()->routeIs('diet.plans.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-heart"></i> Diet Plans
        </a>
    </div>
</div>

{{-- ── Administration ── --}}
<div class="sb-nav-label" style="margin-top:.5rem;">Administration</div>
<a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="bi bi-person-badge"></i> User Management
</a>
<a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
    <i class="bi bi-key"></i> Permission
</a>

<a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
    <i class="bi bi-gear"></i> Settings
</a>


@else
{{-- ──────────────────────────────
     NON-ADMIN: flat links
────────────────────────────── --}}

@if(in_array($role, ['doctor', 'med-tech']))
<div class="sb-nav-label" style="margin-top:.5rem;">Laboratory (LIS)</div>
<a href="{{ route('lab.dashboard') }}" class="nav-link {{ request()->routeIs('lab.dashboard') ? 'active' : '' }}">
    <i class="bi bi-clipboard2-pulse"></i> Lab Dashboard
</a>
<a href="{{ route('lab.requests.index') }}" class="nav-link {{ request()->routeIs('lab.requests.*') ? 'active' : '' }}">
    <i class="bi bi-list-task"></i> Lab Requests
</a>
<a href="{{ route('lab.results.index') }}" class="nav-link {{ request()->routeIs('lab.results.*') ? 'active' : '' }}">
    <i class="bi bi-journal-medical"></i> Lab Results
</a>
@endif

@if(in_array($role, ['doctor', 'rad-tech', 'radiologist']))
<div class="sb-nav-label" style="margin-top:.5rem;">Radiology (RIS)</div>
<a href="{{ route('radiology.dashboard') }}" class="nav-link {{ request()->routeIs('radiology.dashboard') ? 'active' : '' }}">
    <i class="bi bi-activity"></i> Radiology Dashboard
</a>
<a href="{{ route('radiology.requests.index') }}" class="nav-link {{ request()->routeIs('radiology.requests.*') ? 'active' : '' }}">
    <i class="bi bi-image"></i> Imaging Requests
</a>
@if($role === 'radiologist')
<a href="{{ route('radiology.reports.index') }}" class="nav-link {{ request()->routeIs('radiology.reports.*') ? 'active' : '' }}">
    <i class="bi bi-file-text"></i> Radiology Reports
</a>
@endif
@endif

@if(in_array($role, ['doctor', 'pharmacist']))
<div class="sb-nav-label" style="margin-top:.5rem;">Pharmacy (PMS)</div>
<a href="{{ route('pharmacy.dashboard') }}" class="nav-link {{ request()->routeIs('pharmacy.dashboard') ? 'active' : '' }}">
    <i class="bi bi-capsule"></i> Pharmacy Dashboard
</a>
<a href="{{ route('pharmacy.prescriptions.index') }}" class="nav-link {{ request()->routeIs('pharmacy.prescriptions.*') ? 'active' : '' }}">
    <i class="bi bi-prescription2"></i> Prescriptions
</a>
@if($role === 'pharmacist')
<a href="{{ route('pharmacy.dispensing.index') }}" class="nav-link {{ request()->routeIs('pharmacy.dispensing.*') ? 'active' : '' }}">
    <i class="bi bi-bag-plus"></i> Dispensing Records
</a>
@endif
@endif

@if(in_array($role, ['doctor', 'or-coordinator']))
<div class="sb-nav-label" style="margin-top:.5rem;">Surgery (SORS)</div>
<a href="{{ route('surgery.dashboard') }}" class="nav-link {{ request()->routeIs('surgery.dashboard') ? 'active' : '' }}">
    <i class="bi bi-heart-pulse"></i> Surgery Dashboard
</a>
<a href="{{ route('surgery.requests.index') }}" class="nav-link {{ request()->routeIs('surgery.requests.*') ? 'active' : '' }}">
    <i class="bi bi-scissors"></i> Surgery Requests
</a>
@if($role === 'or-coordinator')
<a href="{{ route('surgery.schedules.index') }}" class="nav-link {{ request()->routeIs('surgery.schedules.*') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> OR Schedules
</a>
<a href="{{ route('surgery.calendar') }}" class="nav-link {{ request()->routeIs('surgery.calendar') ? 'active' : '' }}">
    <i class="bi bi-calendar-week"></i> Surgery Calendar
</a>
@endif
@endif

@if(in_array($role, ['doctor', 'dietitian']))
<div class="sb-nav-label" style="margin-top:.5rem;">Nutrition (DNMS)</div>
<a href="{{ route('diet.dashboard') }}" class="nav-link {{ request()->routeIs('diet.dashboard') ? 'active' : '' }}">
    <i class="bi bi-apple"></i> Diet Dashboard
</a>
<a href="{{ route('diet.requests.index') }}" class="nav-link {{ request()->routeIs('diet.requests.*') ? 'active' : '' }}">
    <i class="bi bi-journal-bookmark"></i> Diet Requests
</a>
@if($role === 'dietitian')
<a href="{{ route('diet.plans.index') }}" class="nav-link {{ request()->routeIs('diet.plans.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard2-heart"></i> Diet Plans
</a>
@endif
@endif

{{-- ── Settings Nav Link (For Non-Admin) ── --}}
<a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
    <i class="bi bi-gear"></i> Settings
</a>

@endif {{-- end admin / non-admin --}}

<div style="height: 2rem;"></div>

{{-- ── Dropdown styles (scoped to sidebar, admin only) ── --}}
@once
<style>
    /* ── Group toggle button ── */
    .sb-group-toggle {
        background: none;
        border: none;
        border-left: 3px solid transparent;
        display: flex;
        align-items: center;
        gap: .65rem;
        width: 100%;
        text-align: left;
        font-family: var(--font-body);
        font-size: .84rem;
        font-weight: 400;
        color: rgba(255,255,255,.65);
        padding: .5rem 1.25rem;
        cursor: pointer;
        transition: background .15s, color .15s, border-color .15s;
    }
    .sb-group-toggle:hover {
        background: rgba(255,255,255,.06);
        color: #fff;
        border-left-color: rgba(20,199,154,.4);
    }
    .sb-group-toggle.active {
        background: rgba(20,199,154,.08);
        color: #fff;
        border-left-color: var(--signal);
        font-weight: 600;
    }
    .sb-group-toggle:focus-visible {
        outline: 2px solid var(--signal);
        outline-offset: -2px;
    }
    .sb-group-toggle i:first-child {
        width: 18px;
        text-align: center;
        font-size: 1rem;
        opacity: .7;
        flex-shrink: 0;
    }
    .sb-group-toggle.active i:first-child { opacity: 1; }

    /* Label text + module badge */
    .sb-group-label {
        flex: 1;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .sb-badge {
        font-family: var(--font-mono);
        font-size: .54rem;
        font-weight: 600;
        letter-spacing: .07em;
        background: rgba(20,199,154,.15);
        color: var(--signal);
        border-radius: .25rem;
        padding: .06rem .28rem;
        line-height: 1.5;
    }
    .sb-group-toggle.active .sb-badge {
        background: rgba(20,199,154,.25);
    }

    /* Rotating chevron */
    .sb-chevron {
        font-size: .6rem;
        color: rgba(255,255,255,.3);
        flex-shrink: 0;
        transition: transform .2s ease, color .15s;
    }
    .sb-group-toggle[aria-expanded="true"] .sb-chevron {
        transform: rotate(-180deg);
        color: var(--signal);
    }
    @media (prefers-reduced-motion: reduce) {
        .sb-chevron { transition: none; }
    }

    /* Sub-items */
    .sb-group-body {
        border-left: 1px solid rgba(255,255,255,.07);
        margin-left: 2rem;
    }
    .nav-link.sb-sub {
        font-size: .81rem;
        padding: .38rem .85rem;
        color: rgba(255,255,255,.52);
        border-left: 2px solid transparent;
    }
    .nav-link.sb-sub i {
        font-size: .82rem;
        opacity: .55;
    }
    .nav-link.sb-sub:hover {
        background: rgba(255,255,255,.05);
        color: rgba(255,255,255,.9);
        border-left-color: rgba(20,199,154,.4);
    }
    .nav-link.sb-sub:hover i { opacity: .9; }
    .nav-link.sb-sub.active {
        background: rgba(20,199,154,.1);
        color: #fff;
        border-left-color: var(--signal);
        font-weight: 600;
    }
    .nav-link.sb-sub.active i { opacity: 1; }
</style>
@endonce
