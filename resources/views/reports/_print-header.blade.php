{{-- ── Print Header Partial ── --}}
<div class="print-header d-none">
    <div class="text-center mb-3">
        <h4 class="fw-bold mb-0">Diagnostic, Treatment & Clinical Services</h4>
        <small class="text-muted">Hospital Information Management System</small>
    </div>
    <div class="d-flex justify-content-between border-top border-bottom py-2 mb-3">
        <div>
            <strong>{{ $reportTitle ?? 'Report' }}</strong>
        </div>
        <div class="text-end small">
            <div>Period: {{ $from->format('M d, Y') }} – {{ $to->format('M d, Y') }}</div>
            <div>Generated: {{ now()->format('M d, Y h:i A') }}</div>
        </div>
    </div>
</div>
