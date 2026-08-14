@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Account</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <p class="text-muted" style="font-size: 0.85rem;">Settings are currently managed via the User Account Menu at the bottom of the sidebar.</p>
    </div>
</div>
@endsection
