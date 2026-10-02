<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * PatientController — manages the Patient Information module.
 * Patient records originate from the upstream patient data / registration system.
 * This controller supports read, search, view, and archive (soft-delete) only.
 * Creating or editing patient records from within this subsystem is intentionally disabled.
 *
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
