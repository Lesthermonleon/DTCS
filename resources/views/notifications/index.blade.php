@extends('layouts.app')

@section('title', 'Notification Center')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Notifications</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-bell text-primary me-2"></i>Notification Center
        </h4>
        <p class="text-muted small mb-0">System alerts, clinical updates, and assigned tasks</p>
    </div>
    @if($notifications->count() > 0)
    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            <i class="bi bi-check2-all me-1"></i>Mark all as read
        </button>
    </form>
    @endif
</div>

{{-- Filter Tabs --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-2">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('notifications.index') }}" class="btn btn-sm {{ !request('filter') ? 'btn-primary' : 'btn-light' }} rounded-pill">
                All
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn btn-sm {{ request('filter') === 'unread' ? 'btn-primary' : 'btn-light' }} rounded-pill">
                Unread
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'lis']) }}" class="btn btn-sm {{ request('filter') === 'lis' ? 'btn-primary' : 'btn-light' }} rounded-pill">
                Laboratory (LIS)
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'ris']) }}" class="btn btn-sm {{ request('filter') === 'ris' ? 'btn-primary' : 'btn-light' }} rounded-pill">
                Radiology (RIS)
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'pms']) }}" class="btn btn-sm {{ request('filter') === 'pms' ? 'btn-primary' : 'btn-light' }} rounded-pill">
                Pharmacy (PMS)
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'sors']) }}" class="btn btn-sm {{ request('filter') === 'sors' ? 'btn-primary' : 'btn-light' }} rounded-pill">
                Surgery (SORS)
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'dnms']) }}" class="btn btn-sm {{ request('filter') === 'dnms' ? 'btn-primary' : 'btn-light' }} rounded-pill">
                Dietary (DNMS)
            </a>
        </div>
    </div>
</div>

{{-- Notification List --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @forelse($notifications as $notification)
            <a href="{{ route('notifications.read', $notification) }}" 
               class="d-flex align-items-start gap-3 p-3 text-decoration-none border-bottom transition-all {{ !$notification->is_read ? 'bg-light' : 'bg-white' }} text-dark hover-bg-light">
                
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(20, 199, 154, 0.15); color: var(--signal-dark);">
                    @if($notification->priority === 'critical')
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    @elseif($notification->priority === 'urgent')
                        <i class="bi bi-bell-fill text-warning fs-5"></i>
                    @else
                        <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                    @endif
                </div>

                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-secondary me-2" style="font-size: 0.7rem;">{{ strtoupper($notification->module) }}</span>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : 'fw-semibold' }} text-dark">
                        {{ $notification->title }}
                        @if(!$notification->is_read)
                            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.6rem;">NEW</span>
                        @endif
                    </h6>
                    <p class="mb-0 text-muted small text-truncate" style="max-width: 900px;">
                        {{ $notification->message }}
                    </p>
                </div>
            </a>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted opacity-50 mb-3 d-block"></i>
                <h6 class="fw-semibold text-muted">No notifications found</h6>
                <p class="text-muted small mb-0">When system updates or alerts occur, they will appear here.</p>
            </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
