{{-- Flash message toasts --}}

@if(session('success'))
<div class="toast align-items-center mb-2 custom-toast toast-success" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-content d-flex p-3 w-100">
        <i class="ph-fill ph-check-circle toast-icon"></i>
        <div class="toast-details flex-grow-1 ms-3">
            <div class="toast-title">Success</div>
            <div class="toast-message">{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
@endif

@if(session('error'))
<div class="toast align-items-center mb-2 custom-toast toast-error" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-content d-flex p-3 w-100">
        <i class="ph-fill ph-warning-circle toast-icon"></i>
        <div class="toast-details flex-grow-1 ms-3">
            <div class="toast-title">Error</div>
            <div class="toast-message">{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
@endif

@if(session('warning'))
<div class="toast align-items-center mb-2 custom-toast toast-warning" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-content d-flex p-3 w-100">
        <i class="ph-fill ph-warning toast-icon"></i>
        <div class="toast-details flex-grow-1 ms-3">
            <div class="toast-title">Warning</div>
            <div class="toast-message">{{ session('warning') }}</div>
        </div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
@endif

@if(session('info'))
<div class="toast align-items-center mb-2 custom-toast toast-info" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-content d-flex p-3 w-100">
        <i class="ph-fill ph-info toast-icon"></i>
        <div class="toast-details flex-grow-1 ms-3">
            <div class="toast-title">Information</div>
            <div class="toast-message">{{ session('info') }}</div>
        </div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
@endif
