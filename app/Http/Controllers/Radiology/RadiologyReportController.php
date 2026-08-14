<?php

namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRadiologyReportRequest;
use App\Models\RadiologyReport;
use App\Models\RadiologyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * RadiologyReportController — radiologist creates, approves, and releases diagnostic reports.
 */
class RadiologyReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', RadiologyReport::class);

        $query = RadiologyReport::with('radiologyRequest.patient', 'radiologyRequest.doctor', 'radiologist');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('radiology.reports.index', compact('reports'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', RadiologyReport::class);

        // Show requests that are Completed or In Progress (with uploaded images) and have no report yet
        $pendingRequests = RadiologyRequest::whereIn('status', ['Completed', 'In Progress', 'Scheduled'])
                           ->whereDoesntHave('report')
                           ->with('patient', 'images')
                           ->get();

        $selectedRequestId = $request->input('radiology_request_id');

        return view('radiology.reports.create', compact('pendingRequests', 'selectedRequestId'));
    }

    public function store(StoreRadiologyReportRequest $request): RedirectResponse
    {
        $this->authorize('create', RadiologyReport::class);

        $report = RadiologyReport::create(array_merge($request->validated(), [
            'radiologist_id' => Auth::id(),
            'status'         => 'Draft',
        ]));

        // Ensure radiology request is marked as completed if it wasn't already
        $radReq = RadiologyRequest::find($request->radiology_request_id);
        if ($radReq && $radReq->status !== 'Completed') {
            $radReq->update(['status' => 'Completed', 'completed_at' => now()]);
        }

        return redirect()->route('radiology.reports.show', $report)
                         ->with('success', 'Diagnostic radiology report created successfully as Draft.');
    }

    public function show(RadiologyReport $radiologyReport): View
    {
        $this->authorize('view', $radiologyReport);

        $radiologyReport->load('radiologyRequest.patient', 'radiologyRequest.doctor', 'radiologyRequest.images', 'radiologist', 'approvedBy', 'releasedBy');

        return view('radiology.reports.show', compact('radiologyReport'));
    }

    public function edit(RadiologyReport $radiologyReport): View
    {
        $this->authorize('update', $radiologyReport);

        return view('radiology.reports.edit', compact('radiologyReport'));
    }

    public function update(StoreRadiologyReportRequest $request, RadiologyReport $radiologyReport): RedirectResponse
    {
        $this->authorize('update', $radiologyReport);

        $radiologyReport->update($request->validated());

        return redirect()->route('radiology.reports.show', $radiologyReport)
                         ->with('success', 'Radiology report updated successfully.');
    }

    public function destroy(RadiologyReport $radiologyReport): RedirectResponse
    {
        $this->authorize('delete', $radiologyReport);

        $radiologyReport->delete();

        return redirect()->route('radiology.reports.index')
                         ->with('success', 'Draft report deleted.');
    }

    public function approve(RadiologyReport $radiologyReport): RedirectResponse
    {
        $this->authorize('approve', $radiologyReport);

        $radiologyReport->update([
            'status'      => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Radiology report approved and finalized.');
    }

    public function release(RadiologyReport $radiologyReport): RedirectResponse
    {
        $this->authorize('release', $radiologyReport);

        $radiologyReport->update([
            'status'      => 'Released',
            'released_by' => Auth::id(),
            'released_at' => now(),
        ]);

        return back()->with('success', 'Radiology report officially released to referring doctor.');
    }
}
