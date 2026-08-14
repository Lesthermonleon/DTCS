{{-- ─── Sidebar Navigation (role-aware) ─── --}}
@php
    $role = auth()->user()?->primaryRole;

    // Is any child route in this module currently active?
    $patientsActive = request()->routeIs('patients.*');
    $labActive      = request()->routeIs('lab.*');
    $radActive      = request()->routeIs('radiology.*');
    $pmsActive      = request()->routeIs('pharmacy.*');
    $sorActive      = request()->routeIs('surgery.*');
    $dnmActive      = request()->routeIs('diet.*');
    $reportsActive  = request()->routeIs('reports.*');
    $adminActive    = request()->routeIs('admin.*');
@endphp


@if($role === 'admin')
{{-- ──────────────────────────────
     ADMIN: collapsible dropdowns
────────────────────────────── --}}

<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>

<div class="sb-nav-label" style="margin-top:.5rem;">Clinical Services</div>

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

{{-- ── Patients ── --}}
<div class="sb-nav-label" style="margin-top:.5rem;">Patients</div>
<button class="nav-link sb-group-toggle {{ $patientsActive ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#grp-patients"
        aria-expanded="{{ $patientsActive ? 'true' : 'false' }}" aria-controls="grp-patients">
    <i class="bi bi-people"></i>
    <span class="sb-group-label">Patients</span>
    <i class="bi bi-chevron-down sb-chevron"></i>
</button>
<div class="collapse {{ $patientsActive ? 'show' : '' }}" id="grp-patients">
    <div class="sb-group-body">
        <a href="{{ route('patients.index') }}" class="nav-link sb-sub {{ request()->routeIs('patients.index') || request()->routeIs('patients.show') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i> Patient Directory
        </a>
        <a href="{{ route('patients.create') }}" class="nav-link sb-sub {{ request()->routeIs('patients.create') || request()->routeIs('patients.edit') ? 'active' : '' }}">
            <i class="bi bi-person-plus"></i> New Patient
        </a>
    </div>
</div>

{{-- ── Reports & Analytics ── --}}
<div class="sb-nav-label" style="margin-top:.5rem;">Reports & Analytics</div>
<button class="nav-link sb-group-toggle {{ $reportsActive ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#grp-reports"
        aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" aria-controls="grp-reports">
    <i class="bi bi-file-earmark-bar-graph"></i>
    <span class="sb-group-label">Reports <span class="sb-badge">MODULE</span></span>
    <i class="bi bi-chevron-down sb-chevron"></i>
</button>
<div class="collapse {{ $reportsActive ? 'show' : '' }}" id="grp-reports">
    <div class="sb-group-body">
        <a href="{{ route('reports.index') }}" class="nav-link sb-sub {{ request()->routeIs('reports.index') ? 'active' : '' }}">
            <i class="bi bi-grid"></i> Reports Hub
        </a>
        <a href="{{ route('reports.laboratory.activity') }}" class="nav-link sb-sub {{ request()->routeIs('reports.laboratory.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-pulse"></i> Laboratory
        </a>
        <a href="{{ route('reports.radiology.activity') }}" class="nav-link sb-sub {{ request()->routeIs('reports.radiology.*') ? 'active' : '' }}">
            <i class="bi bi-activity"></i> Radiology
        </a>
        <a href="{{ route('reports.pharmacy.activity') }}" class="nav-link sb-sub {{ request()->routeIs('reports.pharmacy.*') ? 'active' : '' }}">
            <i class="bi bi-capsule"></i> Pharmacy
        </a>
        <a href="{{ route('reports.surgery.activity') }}" class="nav-link sb-sub {{ request()->routeIs('reports.surgery.*') ? 'active' : '' }}">
            <i class="bi bi-heart-pulse"></i> Surgery
        </a>
        <a href="{{ route('reports.diet.activity') }}" class="nav-link sb-sub {{ request()->routeIs('reports.diet.*') ? 'active' : '' }}">
            <i class="bi bi-apple"></i> Diet & Nutrition
        </a>
        <a href="{{ route('reports.clinical.services-summary') }}" class="nav-link sb-sub {{ request()->routeIs('reports.clinical.*') ? 'active' : '' }}">
            <i class="bi bi-pie-chart"></i> Clinical Summary
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




@else
{{-- ──────────────────────────────
     NON-ADMIN: Clinical Services Navigation
────────────────────────────── --}}

@if($role === 'doctor')
<a href="{{ route('doctor.dashboard') }}" class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Main Dashboard
</a>

<div class="sb-nav-label" style="margin-top:.5rem;">Clinical Services</div>

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

{{-- ── Patients ── --}}
<div class="sb-nav-label" style="margin-top:.5rem;">Patients</div>
<button class="nav-link sb-group-toggle {{ $patientsActive ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#grp-patients"
        aria-expanded="{{ $patientsActive ? 'true' : 'false' }}" aria-controls="grp-patients">
    <i class="bi bi-people"></i>
    <span class="sb-group-label">Patients</span>
    <i class="bi bi-chevron-down sb-chevron"></i>
</button>
<div class="collapse {{ $patientsActive ? 'show' : '' }}" id="grp-patients">
    <div class="sb-group-body">
        <a href="{{ route('patients.index') }}" class="nav-link sb-sub {{ request()->routeIs('patients.index') || request()->routeIs('patients.show') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i> Patient Directory
        </a>
        <a href="{{ route('patients.create') }}" class="nav-link sb-sub {{ request()->routeIs('patients.create') || request()->routeIs('patients.edit') ? 'active' : '' }}">
            <i class="bi bi-person-plus"></i> New Patient
        </a>
    </div>
</div>

@else
{{-- ── Single-Department Clinical Roles: Flat Direct Navigation ── --}}
<a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('*.dashboard') && !request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Main Dashboard
</a>

<div class="sb-nav-label" style="margin-top:.5rem;">Clinical Services</div>

@if($role === 'med-tech')
<a href="{{ route('lab.requests.index') }}" class="nav-link {{ request()->routeIs('lab.requests.*') ? 'active' : '' }}">
    <i class="bi bi-list-task"></i> Lab Requests
</a>
<a href="{{ route('lab.results.index') }}" class="nav-link {{ request()->routeIs('lab.results.*') ? 'active' : '' }}">
    <i class="bi bi-journal-medical"></i> Lab Results
</a>
@endif

@if(in_array($role, ['rad-tech', 'radiologist']))
<a href="{{ route('radiology.requests.index') }}" class="nav-link {{ request()->routeIs('radiology.requests.*') ? 'active' : '' }}">
    <i class="bi bi-image"></i> Imaging Requests
</a>
@if($role === 'radiologist')
<a href="{{ route('radiology.reports.index') }}" class="nav-link {{ request()->routeIs('radiology.reports.*') ? 'active' : '' }}">
    <i class="bi bi-file-text"></i> Reports
</a>
@endif
@endif

@if($role === 'pharmacist')
<a href="{{ route('pharmacy.prescriptions.index') }}" class="nav-link {{ request()->routeIs('pharmacy.prescriptions.*') ? 'active' : '' }}">
    <i class="bi bi-prescription2"></i> Prescriptions
</a>
<a href="{{ route('pharmacy.dispensing.index') }}" class="nav-link {{ request()->routeIs('pharmacy.dispensing.*') ? 'active' : '' }}">
    <i class="bi bi-bag-plus"></i> Dispensing
</a>
@endif

@if($role === 'or-coordinator')
<a href="{{ route('surgery.requests.index') }}" class="nav-link {{ request()->routeIs('surgery.requests.*') ? 'active' : '' }}">
    <i class="bi bi-scissors"></i> Surgery Requests
</a>
<a href="{{ route('surgery.schedules.index') }}" class="nav-link {{ request()->routeIs('surgery.schedules.*') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> OR Schedules
</a>
<a href="{{ route('surgery.calendar') }}" class="nav-link {{ request()->routeIs('surgery.calendar') ? 'active' : '' }}">
    <i class="bi bi-calendar-week"></i> Surgery Calendar
</a>
@endif

@if($role === 'dietitian')
<a href="{{ route('diet.requests.index') }}" class="nav-link {{ request()->routeIs('diet.requests.*') ? 'active' : '' }}">
    <i class="bi bi-journal-bookmark"></i> Diet Requests
</a>
<a href="{{ route('diet.plans.index') }}" class="nav-link {{ request()->routeIs('diet.plans.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard2-heart"></i> Diet Plans
</a>
@endif

<div class="sb-nav-label" style="margin-top:.5rem;">Reports</div>
<a href="{{ route('reports.index') }}" class="nav-link {{ $reportsActive ? 'active' : '' }}">
    <i class="bi bi-file-earmark-bar-graph"></i> Reports Hub
</a>

@endif
@endif


{{-- ── Settings Nav Link (For Non-Admin) ── --}}


<div class="sb-nav-label" style="margin-top:.5rem;">Clinical AI Assistant</div>
<a href="{{ route('medisense.index') }}" class="nav-link {{ request()->routeIs('medisense.*') ? 'active' : '' }}">
    <i class="bi bi-cpu" style="color: var(--signal);"></i>
    <span class="flex-grow-1">MediSense AI</span>
    <span class="sb-badge" style="background: rgba(20,199,154,.15); color: var(--signal);">AI</span>
</a>

<div style="height: 2rem;"></div>

{{-- ── Dropdown styles (scoped to sidebar, admin only) ── --}}
@once
<style>
    #sidebar .nav-link {
        border-left: none !important;
        margin: 2px 12px;
        border-radius: 6px;
        padding: 0.55rem 0.85rem;
        color: var(--sidebar-text-soft);
        font-family: var(--font-body);
        font-size: .84rem;
        font-weight: 400;
        display: flex;
        align-items: center;
        gap: .65rem;
        text-decoration: none;
        transition: background-color 0.15s, color 0.15s, transform 0.1s;
    }

    #sidebar .nav-link i:first-child {
        width: 18px;
        text-align: center;
        font-size: 1rem;
        opacity: .75;
        flex-shrink: 0;
        transition: transform 0.15s ease;
    }

    #sidebar .nav-link:hover {
        background: var(--sidebar-hover-bg) !important;
        color: var(--sidebar-hover-text) !important;
    }
    #sidebar .nav-link:hover i:first-child {
        opacity: 1;
        transform: scale(1.05);
    }

    #sidebar .nav-link.active {
        background: rgba(20, 199, 154, 0.12) !important;
        color: var(--signal) !important;
        font-weight: 600;
    }
    #sidebar .nav-link.active i:first-child {
        opacity: 1;
        color: var(--signal);
    }
    #sidebar .nav-link:active {
        transform: scale(0.98);
    }

    /* ── Group toggle button ── */
    .sb-group-toggle {
        background: none;
        border: none !important;
        display: flex;
        align-items: center;
        gap: .65rem;
        width: calc(100% - 24px) !important;
        margin: 2px 12px;
        border-radius: 6px;
        text-align: left;
        font-family: var(--font-body);
        font-size: .84rem;
        font-weight: 400;
        color: var(--sidebar-text-soft);
        padding: 0.55rem 0.85rem;
        cursor: pointer;
        text-transform: none;
        transition: background-color 0.15s, color 0.15s, transform 0.1s;
    }
    
    .sb-group-toggle:hover {
        background: var(--sidebar-hover-bg) !important;
        color: var(--sidebar-hover-text) !important;
    }
    .sb-group-toggle:hover i:first-child {
        opacity: 1;
        transform: scale(1.05);
    }

    .sb-group-toggle.active {
        background: var(--sidebar-active-bg) !important;
        color: var(--sidebar-active-text) !important;
        font-weight: 600;
    }
    .sb-group-toggle.active i:first-child {
        opacity: 1;
        color: var(--signal);
    }
    .sb-group-toggle:active {
        transform: scale(0.98);
    }

    /* Label text + module badge */
    .sb-group-label {
        flex: 1;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .sb-badge {
        font-family: var(--font-mono);
        font-size: .52rem;
        font-weight: 650;
        letter-spacing: .06em;
        background: rgba(20,199,154,.12);
        color: var(--signal);
        border: 1px solid rgba(20,199,154,0.25);
        border-radius: .25rem;
        padding: .06rem .28rem;
        line-height: 1.4;
        transition: background-color 0.15s;
    }
    .sb-group-toggle.active .sb-badge,
    .sb-group-toggle:hover .sb-badge {
        background: rgba(20,199,154,.2);
    }

    /* Rotating chevron */
    .sb-chevron {
        font-size: .65rem;
        color: var(--sidebar-text-soft);
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

    /* Sub-items list body */
    .sb-group-body {
        border-left: 1px solid var(--sidebar-border);
        margin-left: 1.85rem;
        padding-left: 0.1rem;
        margin-top: 1px;
        margin-bottom: 3px;
    }
    .nav-link.sb-sub {
        font-size: .81rem !important;
        padding: .4rem 0.85rem !important;
        color: var(--sidebar-text-soft) !important;
        margin: 1px 12px 1px 4px !important;
        border-radius: 5px !important;
    }
    .nav-link.sb-sub i {
        font-size: .82rem !important;
        opacity: .55;
    }
    .nav-link.sb-sub:hover {
        background: var(--sidebar-hover-bg) !important;
        color: var(--sidebar-hover-text) !important;
    }
    .nav-link.sb-sub:hover i { opacity: .85; }
    .nav-link.sb-sub.active {
        background: var(--sidebar-active-bg) !important;
        color: var(--sidebar-active-text) !important;
        font-weight: 500 !important;
    }
    .nav-link.sb-sub.active i { opacity: 1; }
</style>
@endonce
