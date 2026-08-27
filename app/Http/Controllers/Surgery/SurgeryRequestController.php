<?php

namespace App\Http\Controllers\Surgery;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurgeryRequestRequest;
use App\Models\ActivityLog;
use App\Models\Patient;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * SurgeryRequestController — Doctors submit surgery requests; OR Coordinators manage them.
 */
class SurgeryRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = SurgeryRequest::with('patient', 'doctor', 'schedule.operatingRoom');

        /** @var User|null $user */
        $user = Auth::user();

        if ($user?->hasRole('doctor')) {
            $query->where('doctor_id', Auth::id());
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                  ->orWhere('procedure_name', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('last_name', 'like', "%{$search}%"));
            });
        }

        if ($s = $request->input('status'))  { $query->where('status', $s); }
        if ($u = $request->input('urgency')) { $query->where('urgency', $u); }

        $surgeryRequests = $query->latest()->paginate(15)->withQueryString();
        $statuses  = ['Pending', 'Scheduled', 'In Progress', 'Completed', 'Cancelled'];
        $urgencies = ['Elective', 'Urgent', 'Emergency'];

        return view('surgery.requests.index', compact('surgeryRequests', 'statuses', 'urgencies'));
    }

    public function create(): View
    {
        // Only Doctors may originate surgery requests
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can create surgery requests.');

        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);

        return view('surgery.requests.create', compact('patients'));
    }

    public function store(StoreSurgeryRequestRequest $request): RedirectResponse
    {
        // Only Doctors may originate surgery requests
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can create surgery requests.');

        $count = SurgeryRequest::count() + 1;

        SurgeryRequest::create(array_merge($request->validated(), [
            'request_no'   => 'SR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
            'doctor_id'    => Auth::id(),
            'status'       => 'Pending',
            'requested_at' => now(),
        ]));

        $latest = SurgeryRequest::where('doctor_id', Auth::id())->latest()->first();
        if ($latest) {
            ActivityLog::create([
                'user_id'       => Auth::id(),
                'action'        => 'Surgery Request Created',
                'module'        => 'Surgery',
                'description'   => "Surgery request [{$latest->request_no}] for {$latest->procedure_name} was submitted.",
                'loggable_type' => SurgeryRequest::class,
                'loggable_id'   => $latest->id,
                'ip_address'    => request()->ip(),
                'logged_at'     => now(),
            ]);
        }

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
        // Only the originating Doctor may edit their own request
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can edit surgery requests.');
        abort_if($surgeryRequest->doctor_id !== Auth::id(), 403, 'You can only edit your own surgery requests.');
        abort_if($surgeryRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');

        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);

        return view('surgery.requests.edit', compact('surgeryRequest', 'patients'));
    }

    public function update(StoreSurgeryRequestRequest $request, SurgeryRequest $surgeryRequest): RedirectResponse
    {
        // Only the originating Doctor may update their own request
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can update surgery requests.');
        abort_if($surgeryRequest->doctor_id !== Auth::id(), 403, 'You can only update your own surgery requests.');
        abort_if($surgeryRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');

        $surgeryRequest->update($request->validated());

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Surgery Request Updated',
            'module'        => 'Surgery',
            'description'   => "Surgery request [{$surgeryRequest->request_no}] was updated.",
            'loggable_type' => SurgeryRequest::class,
            'loggable_id'   => $surgeryRequest->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('surgery.requests.show', $surgeryRequest)
                         ->with('success', 'Surgery request updated.');
    }

    public function destroy(SurgeryRequest $surgeryRequest): RedirectResponse
    {
        // Only the originating Doctor may cancel/remove their own request
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can cancel surgery requests.');
        abort_if($surgeryRequest->doctor_id !== Auth::id(), 403, 'You can only cancel your own surgery requests.');
        abort_if($surgeryRequest->status !== 'Pending', 403, 'Only pending requests can be cancelled.');

        $surgeryRequest->update(['status' => 'Cancelled']);

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Surgery Request Cancelled',
            'module'        => 'Surgery',
            'description'   => "Surgery request [{$surgeryRequest->request_no}] was cancelled.",
            'loggable_type' => SurgeryRequest::class,
            'loggable_id'   => $surgeryRequest->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('surgery.requests.index')
                         ->with('success', 'Surgery request cancelled.');
    }

    public function cancel(SurgeryRequest $surgeryRequest): RedirectResponse
    {
        // Only the originating Doctor or OR Coordinator with scheduling authority may use this action
        /** @var User|null $user */
        $user            = Auth::user();
        $isDoctor        = $user?->hasRole('doctor');
        $isOrCoordinator = $user?->hasRole('or-coordinator');

        abort_if(! $isDoctor && ! $isOrCoordinator, 403, 'Unauthorized to cancel surgery requests.');

        // Doctors may only cancel their own request
        if ($isDoctor && ! $isOrCoordinator) {
            abort_if($surgeryRequest->doctor_id !== Auth::id(), 403, 'You can only cancel your own surgery requests.');
        }

        $surgeryRequest->update(['status' => 'Cancelled']);

        return back()->with('success', 'Surgery request cancelled.');
    }
}
