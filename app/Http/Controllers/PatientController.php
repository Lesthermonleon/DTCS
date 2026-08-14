<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        Patient::create($data);

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

        return redirect()->route('patients.show', $patient)
                         ->with('success', 'Patient record updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->authorize('delete', $patient);

        $patient->delete(); // SoftDelete

        return redirect()->route('patients.index')
                         ->with('success', 'Patient record archived successfully.');
    }
}

