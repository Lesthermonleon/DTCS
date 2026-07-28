<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lab Report — {{ $labRequest->request_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h2 { color: #1565c0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1565c0; color: #fff; padding: 6px 8px; text-align: left; font-size: 11px; }
        td { padding: 6px 8px; border-bottom: 1px solid #e0e0e0; }
        .label { color: #666; font-size: 11px; }
        .result-val { font-weight: bold; font-size: 14px; color: #1565c0; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2>DITC Hospital Management System</h2>
            <p class="label">Laboratory Report — {{ $labRequest->request_no }}</p>
        </div>
        <div style="text-align:right;">
            <strong>{{ now()->format('F d, Y') }}</strong><br>
            <span class="label">Printed by: {{ auth()->user()->name }}</span>
        </div>
    </div>
    <hr>
    <table style="margin-bottom:12px">
        <tr>
            <td><span class="label">Patient</span><br><strong>{{ $labRequest->patient->full_name }}</strong></td>
            <td><span class="label">Patient No</span><br>{{ $labRequest->patient->patient_no }}</td>
            <td><span class="label">Requesting Doctor</span><br>{{ $labRequest->doctor->name }}</td>
            <td><span class="label">Specimen</span><br>{{ $labRequest->specimen_type }}</td>
            <td><span class="label">Priority</span><br>{{ $labRequest->priority }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr><th>Test</th><th>Category</th><th>Result</th><th>Unit</th><th>Normal Range</th><th>Remarks</th><th>Status</th></tr>
        </thead>
        <tbody>
        @foreach($labRequest->items as $item)
        <tr>
            <td>{{ $item->labTest->name }}</td>
            <td>{{ $item->labTest->category->name }}</td>
            <td class="result-val">{{ $item->result?->result_value ?? '—' }}</td>
            <td>{{ $item->labTest->unit }}</td>
            <td>{{ $item->labTest->normal_range }}</td>
            <td>{{ $item->result?->remarks ?? '—' }}</td>
            <td>{{ $item->result?->status ?? 'Pending' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:30px; display:flex; gap:60px;">
        <div>______________________<br><small>Performed By (MT)</small></div>
        <div>______________________<br><small>Validated By</small></div>
        <div>______________________<br><small>Pathologist</small></div>
    </div>
    <script>window.print();</script>
</body>
</html>
