<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Models\ActivityLog;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * PatientController — CRUD operations for patient records.
 * Access restricted exclusively to System Administrator and Doctor.
 */
class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Patient::class);

        $query = Patient::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_no', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($type = $request->input('type')) {
            $query->where('patient_type', $type);
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create(): View
    {
        $this->authorize('create', Patient::class);

        return view('patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        $this->authorize('create', Patient::class);

        // Auto-generate patient number
        $count = Patient::withTrashed()->count() + 1;
        $data  = $request->validated();
        $data['patient_no'] = 'P-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $patient = Patient::create($data);

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Patient Created',
            'module'        => 'Patient Records',
            'description'   => "Patient record [{$patient->patient_no}] {$patient->last_name}, {$patient->first_name} was created.",
            'loggable_type' => Patient::class,
            'loggable_id'   => $patient->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('patients.index')
                         ->with('success', 'Patient record created successfully.');
    }

    public function show(Patient $patient): View
    {
        $this->authorize('view', $patient);

        $patient->load([
            'labRequests',
            'radiologyRequests',
            'prescriptions',
            'surgeryRequests',
            'dietRequests',
        ]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        $this->authorize('update', $patient);

        return view('patients.edit', compact('patient'));
    }

    public function update(StorePatientRequest $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $patient->update($request->validated());

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Patient Updated',
            'module'        => 'Patient Records',
            'description'   => "Patient record [{$patient->patient_no}] {$patient->last_name}, {$patient->first_name} was updated.",
            'loggable_type' => Patient::class,
            'loggable_id'   => $patient->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('patients.show', $patient)
                         ->with('success', 'Patient record updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->authorize('delete', $patient);

        $patientNo   = $patient->patient_no;
        $patientName = "{$patient->last_name}, {$patient->first_name}";
        $patientId   = $patient->id;
        $patient->delete(); // SoftDelete

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Patient Archived',
            'module'        => 'Patient Records',
            'description'   => "Patient record [{$patientNo}] {$patientName} was archived.",
            'loggable_type' => Patient::class,
            'loggable_id'   => $patientId,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('patients.index')
                         ->with('success', 'Patient record archived successfully.');
    }
}

