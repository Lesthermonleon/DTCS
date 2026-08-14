<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabRequestRequest;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\LabTest;
use App\Models\Patient;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * LabRequestController — Manages laboratory request lifecycle.
 * Doctors create requests; Medical Technologists receive and process them.
 */
class LabRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = LabRequest::with('patient', 'doctor', 'items.labTest');

        // Role-based scoping: doctors only see their own requests
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->hasRole('doctor')) {
            $query->where('doctor_id', $currentUser->id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('first_name', 'like', "%{$search}%")
                                                       ->orWhere('last_name', 'like', "%{$search}%")
                                                       ->orWhere('patient_no', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $labRequests = $query->latest()->paginate(15)->withQueryString();
        $statuses    = ['Pending', 'In Progress', 'Completed', 'Cancelled'];
        $priorities  = ['Routine', 'Urgent', 'STAT'];

        return view('lab.requests.index', compact('labRequests', 'statuses', 'priorities'));
    }

    public function create(): View
    {
        $patients  = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $labTests  = LabTest::with('category')->where('is_active', true)->orderBy('name')->get();

        return view('lab.requests.create', compact('patients', 'labTests'));
    }

    public function store(StoreLabRequestRequest $request): RedirectResponse
    {
        $count = LabRequest::count() + 1;

        DB::transaction(function () use ($request, $count) {
            $labRequest = LabRequest::create([
                'request_no'     => 'LR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
                'patient_id'     => $request->patient_id,
                'doctor_id'      => Auth::id(),
                'priority'       => $request->priority,
                'specimen_type'  => $request->specimen_type,
                'clinical_notes' => $request->clinical_notes,
                'status'         => 'Pending',
                'requested_at'   => now(),
            ]);

            foreach ($request->tests as $testId) {
                LabRequestItem::create([
                    'lab_request_id' => $labRequest->id,
                    'lab_test_id'    => $testId,
                    'status'         => 'Pending',
                ]);
            }

            $doctorName = $request->user()?->name ?? 'Doctor';
            NotificationService::notifyRole(
                'med-tech',
                'lab_request',
                'New Laboratory Request',
                "New request {$labRequest->request_no} submitted by Dr. {$doctorName}",
                'lis',
                route('lab.requests.show', $labRequest),
                $request->priority === 'STAT' ? 'critical' : ($request->priority === 'Urgent' ? 'urgent' : 'normal')
            );
        });

        return redirect()->route('lab.requests.index')
                         ->with('success', 'Laboratory request created successfully.');
    }

    public function show(LabRequest $labRequest): View
    {
        $labRequest->load('patient', 'doctor', 'items.labTest.category', 'items.result.technologist');

        return view('lab.requests.show', compact('labRequest'));
    }

    public function edit(LabRequest $labRequest): View
    {
        abort_if($labRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');

        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $labTests = LabTest::with('category')->where('is_active', true)->orderBy('name')->get();

        return view('lab.requests.edit', compact('labRequest', 'patients', 'labTests'));
    }

    public function update(StoreLabRequestRequest $request, LabRequest $labRequest): RedirectResponse
    {
        abort_if($labRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');

        DB::transaction(function () use ($request, $labRequest) {
            $labRequest->update([
                'priority'       => $request->priority,
                'specimen_type'  => $request->specimen_type,
                'clinical_notes' => $request->clinical_notes,
            ]);

            // Refresh items: delete existing and re-create
            $labRequest->items()->delete();
            foreach ($request->tests as $testId) {
                LabRequestItem::create([
                    'lab_request_id' => $labRequest->id,
                    'lab_test_id'    => $testId,
                    'status'         => 'Pending',
                ]);
            }
        });

        return redirect()->route('lab.requests.show', $labRequest)
                         ->with('success', 'Lab request updated successfully.');
    }

    public function destroy(LabRequest $labRequest): RedirectResponse
    {
        abort_if($labRequest->status !== 'Pending', 403, 'Only pending requests can be cancelled.');

        $labRequest->update(['status' => 'Cancelled']);

        return redirect()->route('lab.requests.index')
                         ->with('success', 'Lab request cancelled.');
    }

    /** Medical Technologist / Admin marks request as received. */
    public function receive(LabRequest $labRequest): RedirectResponse
    {
        abort_if($labRequest->status !== 'Pending', 400, 'Only pending laboratory requests can be marked as received.');

        DB::transaction(function () use ($labRequest) {
            $labRequest->update([
                'status'      => 'In Progress',
                'received_at' => now(),
            ]);

            $labRequest->items()
                ->where('status', 'Pending')
                ->update(['status' => 'In Progress']);
        });

        return back()->with('success', "Request {$labRequest->request_no} marked as received.");
    }

    /** Print-friendly view for lab request report. */
    public function print(LabRequest $labRequest): View
    {
        $labRequest->load('patient', 'doctor', 'items.labTest.category', 'items.result');

        return view('lab.requests.print', compact('labRequest'));
    }
}
