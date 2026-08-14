@extends('layouts.app')

@section('title', 'Skeleton System Demo')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <div class="page-eyebrow">DEVELOPER REFERENCE</div>
        <h1 class="page-title">Skeleton Loading System</h1>
        <p class="text-soft" style="font-size:.875rem;">
            All skeleton components available in this project — copy the usage snippet and paste it into any Blade view.
        </p>
    </div>
</div>

{{-- ════════════════════════ STAT CARDS ════════════════════════ --}}
<div class="skeleton-card mb-4">
    <h5 class="sk-section-label mb-3"><i class="bi bi-box-seam me-1 text-primary"></i> Stat Cards — <code>&lt;x-skeleton.stat-card color="signal|coral|amber|steel" /&gt;</code></h5>
    <div class="row g-3">
        <div class="col-6 col-md-3"><x-skeleton.stat-card color="signal" /></div>
        <div class="col-6 col-md-3"><x-skeleton.stat-card color="coral"  /></div>
        <div class="col-6 col-md-3"><x-skeleton.stat-card color="amber"  /></div>
        <div class="col-6 col-md-3"><x-skeleton.stat-card color="steel"  /></div>
    </div>
</div>

{{-- ════════════════════════ TABLE ════════════════════════ --}}
<div class="skeleton-card mb-4">
    <h5 class="sk-section-label mb-3"><i class="bi bi-table me-1 text-primary"></i> Table — <code>&lt;x-skeleton.table :rows="6" :cols="5" /&gt;</code></h5>
    <x-skeleton.table :rows="5" :cols="5" />
</div>

{{-- ════════════════════════ FORMS ════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <h5 class="sk-section-label mb-2"><i class="bi bi-pencil-square me-1 text-primary"></i> Form 1-col — <code>&lt;x-skeleton.form :fields="4" /&gt;</code></h5>
        <x-skeleton.form :fields="4" />
    </div>
    <div class="col-md-6">
        <h5 class="sk-section-label mb-2"><i class="bi bi-pencil-square me-1 text-primary"></i> Form 2-col — <code>&lt;x-skeleton.form :fields="6" :columns="2" /&gt;</code></h5>
        <x-skeleton.form :fields="6" :columns="2" />
    </div>
</div>

{{-- ════════════════════════ CARDS ════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <h5 class="sk-section-label mb-2"><i class="bi bi-card-heading me-1 text-primary"></i> Card — <code>&lt;x-skeleton.card /&gt;</code></h5>
        <x-skeleton.card />
    </div>
    <div class="col-md-8">
        <h5 class="sk-section-label mb-2"><i class="bi bi-card-heading me-1 text-primary"></i> Card horizontal — <code>&lt;x-skeleton.card :count="2" :horizontal="true" /&gt;</code></h5>
        <x-skeleton.card :count="2" :horizontal="true" />
    </div>
</div>

{{-- ════════════════════════ PROFILE ════════════════════════ --}}
<div class="mb-4">
    <h5 class="sk-section-label mb-3"><i class="bi bi-person me-1 text-primary"></i> Profile — <code>&lt;x-skeleton.profile /&gt;</code></h5>
    <x-skeleton.profile />
</div>

{{-- ════════════════════════ MODAL TYPES ════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <h5 class="sk-section-label mb-2"><i class="bi bi-window me-1 text-primary"></i> Modal Form — <code>type="form"</code></h5>
        <div class="skeleton-card">
            <x-skeleton.modal type="form" />
        </div>
    </div>
    <div class="col-md-4">
        <h5 class="sk-section-label mb-2"><i class="bi bi-window me-1 text-primary"></i> Modal Detail — <code>type="detail"</code></h5>
        <div class="skeleton-card">
            <x-skeleton.modal type="detail" />
        </div>
    </div>
    <div class="col-md-4">
        <h5 class="sk-section-label mb-2"><i class="bi bi-window-stack me-1 text-primary"></i> Modal Confirm — <code>type="confirm"</code></h5>
        <div class="skeleton-card">
            <x-skeleton.modal type="confirm" />
        </div>
    </div>
</div>

{{-- ════════════════════════ DASHBOARD FULL ════════════════════════ --}}
<div class="mb-4">
    <h5 class="sk-section-label mb-3"><i class="bi bi-hospital me-1 text-primary"></i> Full Dashboard — <code>&lt;x-skeleton.dashboard :stats="4" :cards="2" /&gt;</code></h5>
    <div style="background:var(--paper);border-radius:.75rem;padding:1.5rem;border:1px solid var(--line);">
        <x-skeleton.dashboard :stats="4" :cards="2" />
    </div>
</div>

{{-- ════════════════════════ SHOW/HIDE DEMO ════════════════════════ --}}
<div class="skeleton-card mb-4">
    <h5 class="sk-section-label mb-3"><i class="bi bi-lightning-charge me-1 text-primary"></i> JS Show/Hide Demo</h5>
    <p class="text-soft" style="font-size:.875rem;margin-bottom:1rem;">
        Click the button to toggle the skeleton on and off dynamically.
    </p>

    <button class="btn btn-sm btn-outline-secondary mb-3" id="demoToggle">
        Show Skeleton
    </button>

    {{-- Skeleton panel --}}
    <div id="demoSkeleton" class="sk-hidden">
        <x-skeleton.table :rows="3" :cols="4" :searchBar="false" :pagination="false" />
    </div>

    {{-- Actual content --}}
    <div id="demoContent" class="sk-content sk-loaded">
        <div class="skeleton-card p-0 overflow-hidden">
            <div class="skeleton-table-header">
                <div style="flex:1;font-family:var(--font-mono);font-size:.72rem;color:var(--text-soft);">REQUEST NO</div>
                <div style="flex:2;">PATIENT</div>
                <div style="flex:1;font-family:var(--font-mono);font-size:.72rem;color:var(--text-soft);">STATUS</div>
                <div style="flex:1;font-family:var(--font-mono);font-size:.72rem;color:var(--text-soft);">DATE</div>
            </div>
            @foreach(['LR-2026-0001','LR-2026-0002','LR-2026-0003'] as $row)
            <div class="skeleton-table-row">
                <div style="flex:1;font-family:var(--font-mono);font-size:.8rem;color:var(--signal);">{{ $row }}</div>
                <div style="flex:2;font-size:.875rem;">Sample Patient</div>
                <div style="flex:1;"><span class="badge" style="background:rgba(20,199,154,.12);color:#0C8F6F;font-family:var(--font-mono);font-size:.7rem;">IN PROGRESS</span></div>
                <div style="flex:1;font-family:var(--font-mono);font-size:.75rem;color:var(--text-soft);">2026-07-18</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ════════════════════════ PRIMITIVE UTILITIES ════════════════════════ --}}
<div class="skeleton-card mb-4">
    <h5 class="sk-section-label mb-3"><i class="bi bi-tools me-1 text-primary"></i> Raw Primitive Classes</h5>
    <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-3">
            <code class="text-soft" style="width:180px;font-size:.75rem;">.sk .sk-xs</code>
            <div class="sk sk-xs" style="width:200px;"></div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <code class="text-soft" style="width:180px;font-size:.75rem;">.sk .sk-sm</code>
            <div class="sk sk-sm" style="width:240px;"></div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <code class="text-soft" style="width:180px;font-size:.75rem;">.sk .sk-md</code>
            <div class="sk sk-md" style="width:300px;"></div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <code class="text-soft" style="width:180px;font-size:.75rem;">.sk .sk-xl</code>
            <div class="sk sk-xl" style="width:120px;border-radius:8px;"></div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <code class="text-soft" style="width:180px;font-size:.75rem;">.sk .sk-circle</code>
            <div class="sk sk-avatar-md"></div>
            <div class="sk sk-avatar-lg"></div>
            <div class="sk sk-avatar-xl"></div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <code class="text-soft" style="width:180px;font-size:.75rem;">.sk .sk-pill (badges)</code>
            <div class="sk sk-sm sk-pill" style="width:60px;"></div>
            <div class="sk sk-sm sk-pill" style="width:80px;"></div>
            <div class="sk sk-sm sk-pill" style="width:50px;"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
var skeleton  = document.getElementById('demoSkeleton');
var content   = document.getElementById('demoContent');
var btn       = document.getElementById('demoToggle');
var showing   = false;

btn.addEventListener('click', function () {
    showing = !showing;
    SkeletonLoader.toggle(skeleton, content, showing);
    btn.textContent = showing ? 'Hide Skeleton (Show Content)' : 'Show Skeleton';
});
</script>

<style>
.sk-section-label {
    font-family: var(--font-display);
    font-size: .9rem;
    font-weight: 600;
    color: var(--text);
}
</style>
@endpush
@endsection
