<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrescriptionRequest;
use App\Models\ActivityLog;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        /** @var User|null $user */
        $user = Auth::user();
        if ($user?->hasRole('doctor')) {
            $query->where('doctor_id', Auth::id());
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('prescription_no', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('first_name', 'like', "%{$search}%")
                                                       ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        if ($s = $request->input('status')) { $query->where('status', $s); }

        $prescriptions = $query->latest()->paginate(15)->withQueryString();
        $statuses      = ['Pending', 'Verified', 'Partially Dispensed', 'Dispensed', 'Cancelled'];

        return view('pharmacy.prescriptions.index', compact('prescriptions', 'statuses'));
    }

    public function create(): View
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can create prescriptions.');
        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);

        return view('pharmacy.prescriptions.create', compact('patients'));
    }

    public function store(StorePrescriptionRequest $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can create prescriptions.');
        $count = Prescription::count() + 1;

        DB::transaction(function () use ($request, $count) {
            $prescription = Prescription::create([
                'prescription_no' => 'RX-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
                'patient_id'      => $request->patient_id,
                'doctor_id'       => Auth::id(),
                'diagnosis'       => $request->diagnosis,
                'notes'           => $request->notes,
                'status'          => 'Pending',
                'prescribed_at'   => now(),
            ]);

            foreach ($request->items as $item) {
                PrescriptionItem::create(array_merge($item, ['prescription_id' => $prescription->id]));
            }
        });

        $latest = Prescription::where('doctor_id', Auth::id())->latest()->first();
        if ($latest) {
            ActivityLog::create([
                'user_id'       => Auth::id(),
                'action'        => 'Prescription Created',
                'module'        => 'Pharmacy',
                'description'   => "Prescription [{$latest->prescription_no}] was created.",
                'loggable_type' => Prescription::class,
                'loggable_id'   => $latest->id,
                'ip_address'    => request()->ip(),
                'logged_at'     => now(),
            ]);
        }

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
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can edit prescriptions.');
        abort_if($prescription->status !== 'Pending', 403, 'Only pending prescriptions can be edited.');
        $patients = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);

        return view('pharmacy.prescriptions.edit', compact('prescription', 'patients'));
    }

    public function update(StorePrescriptionRequest $request, Prescription $prescription): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can edit prescriptions.');
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

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Prescription Updated',
            'module'        => 'Pharmacy',
            'description'   => "Prescription [{$prescription->prescription_no}] was updated.",
            'loggable_type' => Prescription::class,
            'loggable_id'   => $prescription->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('pharmacy.prescriptions.show', $prescription)
                         ->with('success', 'Prescription updated.');
    }

    public function destroy(Prescription $prescription): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can cancel prescriptions.');
        abort_if($prescription->status !== 'Pending', 403, 'Only pending prescriptions can be cancelled.');
        $prescription->update(['status' => 'Cancelled']);

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Prescription Cancelled',
            'module'        => 'Pharmacy',
            'description'   => "Prescription [{$prescription->prescription_no}] was cancelled.",
            'loggable_type' => Prescription::class,
            'loggable_id'   => $prescription->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('pharmacy.prescriptions.index')
                         ->with('success', 'Prescription cancelled.');
    }

    /** Pharmacist verifies the prescription before dispensing. */
    public function verify(Prescription $prescription): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('pharmacist'), 403, 'Only pharmacists can verify prescriptions.');
        abort_if($prescription->status !== 'Pending', 403, 'Only pending prescriptions can be verified.');

        $prescription->update([
            'status'      => 'Verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Prescription Verified',
            'module'        => 'Pharmacy',
            'description'   => "Prescription [{$prescription->prescription_no}] was verified by pharmacist.",
            'loggable_type' => Prescription::class,
            'loggable_id'   => $prescription->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return back()->with('success', 'Prescription verified. Ready for dispensing.');
    }

    /** Print-friendly view for prescription (Rx). */
    public function print(Prescription $prescription): View
    {
        $prescription->load('patient', 'doctor', 'items');

        return view('pharmacy.prescriptions.print', compact('prescription'));
    }
}
