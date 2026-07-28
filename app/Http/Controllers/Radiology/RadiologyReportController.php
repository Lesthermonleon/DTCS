<?php

namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRadiologyReportRequest;
use App\Models\RadiologyReport;
use App\Models\RadiologyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * RadiologyReportController — radiologist creates, approves, and releases reports.
 */
class RadiologyReportController extends Controller
{
    public function index(): View
    {
        $reports = RadiologyReport::with('radiologyRequest.patient', 'radiologist')
                   ->latest()->paginate(15);

        return view('radiology.reports.index', compact('reports'));
    }

    public function create(): View
    {
        // Only show requests that are In Progress and have no report yet
        $pendingRequests = RadiologyRequest::whereIn('status', ['In Progress', 'Scheduled'])
                           ->whereDoesntHave('report')
                           ->with('patient')
                           ->get();

        return view('radiology.reports.create', compact('pendingRequests'));
    }

    public function store(StoreRadiologyReportRequest $request): RedirectResponse
    {
        RadiologyReport::create(array_merge($request->validated(), [
            'radiology_request_id' => $request->radiology_request_id,
            'radiologist_id'       => auth()->id(),
            'status'               => 'Draft',
        ]));

        // Update the request status to completed
        RadiologyRequest::find($request->radiology_request_id)
                        ->update(['status' => 'Completed', 'completed_at' => now()]);

        return redirect()->route('radiology.reports.index')
                         ->with('success', 'Radiology report created successfully.');
    }

    public function show(RadiologyReport $radiologyReport): View
    {
        $radiologyReport->load('radiologyRequest.patient', 'radiologyRequest.doctor', 'radiologist', 'approvedBy', 'releasedBy');

        return view('radiology.reports.show', compact('radiologyReport'));
    }

    public function edit(RadiologyReport $radiologyReport): View
    {
        abort_if($radiologyReport->status === 'Released', 403, 'Released reports cannot be edited.');

        return view('radiology.reports.edit', compact('radiologyReport'));
    }

    public function update(StoreRadiologyReportRequest $request, RadiologyReport $radiologyReport): RedirectResponse
    {
        abort_if($radiologyReport->status === 'Released', 403, 'Released reports cannot be edited.');
        $radiologyReport->update($request->validated());

        return redirect()->route('radiology.reports.show', $radiologyReport)
                         ->with('success', 'Report updated.');
    }

    public function destroy(RadiologyReport $radiologyReport): RedirectResponse
    {
        abort_if($radiologyReport->status !== 'Draft', 403, 'Only draft reports can be deleted.');
        $radiologyReport->delete();

        return redirect()->route('radiology.reports.index')
                         ->with('success', 'Report deleted.');
    }

    public function approve(RadiologyReport $radiologyReport): RedirectResponse
    {
        abort_if($radiologyReport->status !== 'Draft', 403, 'Only draft reports can be approved.');

        $radiologyReport->update([
            'status'      => 'Approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Report approved.');
    }

    public function release(RadiologyReport $radiologyReport): RedirectResponse
    {
        abort_if($radiologyReport->status !== 'Approved', 403, 'Only approved reports can be released.');

        $radiologyReport->update([
            'status'      => 'Released',
            'released_by' => auth()->id(),
            'released_at' => now(),
        ]);

        return back()->with('success', 'Report released to referring doctor.');
    }
}
