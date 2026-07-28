<?php

namespace App\Http\Controllers\Surgery;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurgeryRequestRequest;
use App\Models\Patient;
use App\Models\SurgeryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SurgeryRequestController — Doctors submit surgery requests; OR Coordinators manage them.
 */
class SurgeryRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = SurgeryRequest::with('patient', 'doctor', 'schedule.operatingRoom');

        if (auth()->user()->hasRole('doctor')) {
            $query->where('doctor_id', auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                  ->orWhere('procedure_name', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('last_name', 'like', "%{$search}%"));
            });
        }

        if ($s = $request->get('status'))  { $query->where('status', $s); }
        if ($u = $request->get('urgency')) { $query->where('urgency', $u); }

        $surgeryRequests = $query->latest()->paginate(15)->withQueryString();
        $statuses  = ['Pending', 'Scheduled', 'In Progress', 'Completed', 'Cancelled'];
        $urgencies = ['Elective', 'Urgent', 'Emergency'];

        return view('surgery.requests.index', compact('surgeryRequests', 'statuses', 'urgencies'));
    }

    public function create(): View
    {
        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);

        return view('surgery.requests.create', compact('patients'));
    }

    public function store(StoreSurgeryRequestRequest $request): RedirectResponse
    {
        $count = SurgeryRequest::count() + 1;

        SurgeryRequest::create(array_merge($request->validated(), [
            'request_no'   => 'SR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
            'doctor_id'    => auth()->id(),
            'status'       => 'Pending',
            'requested_at' => now(),
        ]));

        return redirect()->route('surgery.requests.index')
                         ->with('success', 'Surgery request submitted successfully.');
    }

    public function show(SurgeryRequest $surgeryRequest): View
    {
        $surgeryRequest->load('patient', 'doctor', 'schedule.operatingRoom', 'schedule.surgicalTeam.surgeon', 'schedule.surgicalTeam.members.user');

        return view('surgery.requests.show', compact('surgeryRequest'));
    }

    public function edit(SurgeryRequest $surgeryRequest): View
    {
        abort_if($surgeryRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');
        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);

        return view('surgery.requests.edit', compact('surgeryRequest', 'patients'));
    }

    public function update(StoreSurgeryRequestRequest $request, SurgeryRequest $surgeryRequest): RedirectResponse
    {
        abort_if($surgeryRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');
        $surgeryRequest->update($request->validated());

        return redirect()->route('surgery.requests.show', $surgeryRequest)
                         ->with('success', 'Surgery request updated.');
    }

    public function destroy(SurgeryRequest $surgeryRequest): RedirectResponse
    {
        abort_if($surgeryRequest->status !== 'Pending', 403, 'Only pending requests can be cancelled.');
        $surgeryRequest->update(['status' => 'Cancelled']);

        return redirect()->route('surgery.requests.index')
                         ->with('success', 'Surgery request cancelled.');
    }

    public function cancel(SurgeryRequest $surgeryRequest): RedirectResponse
    {
        $surgeryRequest->update(['status' => 'Cancelled']);

        return back()->with('success', 'Surgery request cancelled.');
    }
}
