<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabResultRequest;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\LabResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * LabResultController — Encodes, validates, and releases lab results.
 */
class LabResultController extends Controller
{
    public function index(Request $request): View
    {
        $query = LabResult::with('requestItem.labTest', 'requestItem.labRequest.patient', 'technologist');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $labResults = $query->latest()->paginate(15)->withQueryString();
        $statuses   = ['Pending', 'Encoded', 'Validated', 'Released'];

        return view('lab.results.index', compact('labResults', 'statuses'));
    }

    public function create(): View
    {
        // List pending items that don't have a result yet
        $pendingItems = LabRequestItem::whereDoesntHave('result')
                        ->with('labRequest.patient', 'labTest')
                        ->get();

        return view('lab.results.create', compact('pendingItems'));
    }

    public function store(StoreLabResultRequest $request): RedirectResponse
    {
        LabResult::create([
            'lab_request_item_id' => $request->lab_request_item_id,
            'technologist_id'     => auth()->id(),
            'result_value'        => $request->result_value,
            'remarks'             => $request->remarks,
            'status'              => 'Encoded',
        ]);

        // Update parent item status
        LabRequestItem::find($request->lab_request_item_id)->update(['status' => 'Completed']);

        return redirect()->route('lab.results.index')
                         ->with('success', 'Lab result encoded successfully.');
    }

    public function show(LabResult $labResult): View
    {
        $labResult->load('requestItem.labTest.category', 'requestItem.labRequest.patient', 'technologist', 'validatedBy', 'releasedBy');

        return view('lab.results.show', compact('labResult'));
    }

    public function edit(LabResult $labResult): View
    {
        abort_if($labResult->status === 'Released', 403, 'Released results cannot be edited.');

        return view('lab.results.edit', compact('labResult'));
    }

    public function update(StoreLabResultRequest $request, LabResult $labResult): RedirectResponse
    {
        abort_if($labResult->status === 'Released', 403, 'Released results cannot be edited.');

        $labResult->update([
            'result_value' => $request->result_value,
            'remarks'      => $request->remarks,
            'status'       => 'Encoded',
        ]);

        return redirect()->route('lab.results.show', $labResult)
                         ->with('success', 'Result updated successfully.');
    }

    public function destroy(LabResult $labResult): RedirectResponse
    {
        abort_if($labResult->status !== 'Encoded', 403, 'Only encoded results can be deleted.');
        $labResult->delete();

        return redirect()->route('lab.results.index')
                         ->with('success', 'Result deleted.');
    }

    /** Validate (approve) a lab result before release. */
    public function validate(LabResult $labResult): RedirectResponse
    {
        abort_if($labResult->status !== 'Encoded', 403, 'Only encoded results can be validated.');

        $labResult->update([
            'status'       => 'Validated',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Result validated successfully.');
    }

    /** Release the validated result to the ordering doctor. */
    public function release(LabResult $labResult): RedirectResponse
    {
        abort_if($labResult->status !== 'Validated', 403, 'Only validated results can be released.');

        $labResult->update([
            'status'       => 'Released',
            'released_by'  => auth()->id(),
            'released_at'  => now(),
        ]);

        // Check if all items are completed → update request status
        $labRequest = $labResult->requestItem->labRequest;
        $allReleased = $labRequest->items()
                       ->whereDoesntHave('result', fn($q) => $q->where('status', 'Released'))
                       ->doesntExist();

        if ($allReleased) {
            $labRequest->update(['status' => 'Completed', 'completed_at' => now()]);
        }

        return back()->with('success', 'Result released successfully.');
    }
}
