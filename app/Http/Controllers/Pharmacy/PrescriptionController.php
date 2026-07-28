<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * PrescriptionController — manages doctor prescriptions and pharmacist verification.
 */
class PrescriptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Prescription::with('patient', 'doctor', 'items');

        if (auth()->user()->hasRole('doctor')) {
            $query->where('doctor_id', auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('prescription_no', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('first_name', 'like', "%{$search}%")
                                                       ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        if ($s = $request->get('status')) { $query->where('status', $s); }

        $prescriptions = $query->latest()->paginate(15)->withQueryString();
        $statuses      = ['Pending', 'Verified', 'Partially Dispensed', 'Dispensed', 'Cancelled'];

        return view('pharmacy.prescriptions.index', compact('prescriptions', 'statuses'));
    }

    public function create(): View
    {
        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);

        return view('pharmacy.prescriptions.create', compact('patients'));
    }

    public function store(StorePrescriptionRequest $request): RedirectResponse
    {
        $count = Prescription::count() + 1;

        DB::transaction(function () use ($request, $count) {
            $prescription = Prescription::create([
                'prescription_no' => 'RX-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
                'patient_id'      => $request->patient_id,
                'doctor_id'       => auth()->id(),
                'diagnosis'       => $request->diagnosis,
                'notes'           => $request->notes,
                'status'          => 'Pending',
                'prescribed_at'   => now(),
            ]);

            foreach ($request->items as $item) {
                PrescriptionItem::create(array_merge($item, ['prescription_id' => $prescription->id]));
            }
        });

        return redirect()->route('pharmacy.prescriptions.index')
                         ->with('success', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription): View
    {
        $prescription->load('patient', 'doctor', 'items.dispensingRecords.pharmacist', 'verifiedBy');

        return view('pharmacy.prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription): View
    {
        abort_if($prescription->status !== 'Pending', 403, 'Only pending prescriptions can be edited.');
        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);

        return view('pharmacy.prescriptions.edit', compact('prescription', 'patients'));
    }

    public function update(StorePrescriptionRequest $request, Prescription $prescription): RedirectResponse
    {
        abort_if($prescription->status !== 'Pending', 403, 'Only pending prescriptions can be edited.');

        DB::transaction(function () use ($request, $prescription) {
            $prescription->update([
                'diagnosis' => $request->diagnosis,
                'notes'     => $request->notes,
            ]);

            $prescription->items()->delete();
            foreach ($request->items as $item) {
                PrescriptionItem::create(array_merge($item, ['prescription_id' => $prescription->id]));
            }
        });

        return redirect()->route('pharmacy.prescriptions.show', $prescription)
                         ->with('success', 'Prescription updated.');
    }

    public function destroy(Prescription $prescription): RedirectResponse
    {
        abort_if($prescription->status !== 'Pending', 403, 'Only pending prescriptions can be cancelled.');
        $prescription->update(['status' => 'Cancelled']);

        return redirect()->route('pharmacy.prescriptions.index')
                         ->with('success', 'Prescription cancelled.');
    }

    /** Pharmacist verifies the prescription before dispensing. */
    public function verify(Prescription $prescription): RedirectResponse
    {
        abort_if($prescription->status !== 'Pending', 403, 'Only pending prescriptions can be verified.');

        $prescription->update([
            'status'      => 'Verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Prescription verified. Ready for dispensing.');
    }
}
