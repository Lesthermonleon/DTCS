@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body text-center py-5">
        <div style="width:56px;height:56px;border-radius:.75rem;background:rgba(20,199,154,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="bi bi-speedometer2" style="font-size:1.5rem;color:var(--signal-dark);"></i>
        </div>
        <h5 style="font-family:var(--font-display);font-weight:600;color:var(--text);margin-bottom:1.5rem;">
            Use the sidebar navigation to access your clinical modules.
        </h5>
        <a href="{{ route('patients.index') }}"
           style="display:inline-flex;align-items:center;gap:.45rem;background:rgba(20,199,154,.12);color:var(--signal-dark);border:1px solid rgba(20,199,154,.25);border-radius:.55rem;padding:.5rem 1.1rem;font-weight:600;font-size:.84rem;text-decoration:none;">
            <i class="bi bi-people"></i> View Patients
        </a>
    </div>
</div>
@endsection
