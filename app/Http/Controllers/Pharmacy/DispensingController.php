<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDispensingRecordRequest;
use App\Models\DispensingRecord;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * DispensingController — manages medication dispensing workflow by pharmacists.
 */
class DispensingController extends Controller
{
    public function index(Request $request): View
    {
        $query = DispensingRecord::with([
            'prescriptionItem.prescription.patient',
            'prescriptionItem.prescription.doctor',
            'pharmacist'
        ]);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('prescriptionItem.prescription', fn($p) => $p->where('prescription_no', 'like', "%{$search}%"))
                  ->orWhereHas('prescriptionItem.prescription.patient', fn($pt) => $pt->where('first_name', 'like', "%{$search}%")
                                                                                      ->orWhere('last_name', 'like', "%{$search}%")
                                                                                      ->orWhere('patient_no', 'like', "%{$search}%"))
                  ->orWhereHas('prescriptionItem', fn($item) => $item->where('medication_name', 'like', "%{$search}%"))
                  ->orWhere('lot_number', 'like', "%{$search}%");
            });
        }

        $records = $query->latest('dispensed_at')->paginate(15)->withQueryString();

        $today = now()->toDateString();
        $stats = [
            'dispensed_today'   => DispensingRecord::whereDate('dispensed_at', $today)->count(),
            'dispensed_month'   => DispensingRecord::whereMonth('dispensed_at', now()->month)
                                                    ->whereYear('dispensed_at', now()->year)->count(),
            'ready_to_dispense' => Prescription::whereIn('status', ['Verified', 'Partially Dispensed'])
                                                  ->whereHas('items', fn($q) => $q->where('status', 'Pending'))
                                                  ->count(),
            'total_dispensings' => DispensingRecord::count(),
        ];

        return view('pharmacy.dispensing.index', compact('records', 'stats'));
    }

    public function create(Request $request): View
    {
        abort_if(! Auth::user()?->hasRole('pharmacist'), 403, 'Only pharmacists can dispense medications.');

        // Verified and Partially Dispensed prescriptions that still have pending items
        $prescriptions = Prescription::whereIn('status', ['Verified', 'Partially Dispensed'])
            ->whereHas('items', fn($q) => $q->where('status', 'Pending'))
            ->with(['patient', 'doctor', 'items' => fn($q) => $q->where('status', 'Pending')])
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedPrescription = null;
        if ($rxId = $request->get('rx')) {
            $selectedPrescription = Prescription::with(['patient', 'doctor', 'items'])->find($rxId);
        }

        $selectedItem = null;
        if ($itemId = $request->get('item')) {
            $selectedItem = PrescriptionItem::with('prescription.patient')->find($itemId);
            if ($selectedItem && ! $selectedPrescription) {
                $selectedPrescription = $selectedItem->prescription;
            }
        }

        return view('pharmacy.dispensing.create', compact('prescriptions', 'selectedPrescription', 'selectedItem'));
    }

    public function store(StoreDispensingRecordRequest $request): RedirectResponse
    {
        abort_if(! Auth::user()?->hasRole('pharmacist'), 403, 'Only pharmacists can dispense medications.');

        $item = PrescriptionItem::with('prescription')->findOrFail($request->prescription_item_id);

        if ($item->status === 'Dispensed') {
            return back()->with('error', 'This medication item has already been dispensed.')
                         ->withInput();
        }

        $record = DB::transaction(function () use ($request, $item) {
            // 1. Create dispensing record
            $dispensing = DispensingRecord::create([
                'prescription_item_id' => $item->id,
                'pharmacist_id'        => Auth::id(),
                'quantity_dispensed'   => $request->quantity_dispensed,
                'lot_number'           => $request->lot_number,
                'expiry_date'          => $request->expiry_date,
                'notes'                => $request->notes,
                'dispensed_at'         => now(),
            ]);

            // 2. Mark item as dispensed
            $item->update(['status' => 'Dispensed']);

            // 3. Recalculate parent prescription status
            $prescription = $item->prescription;
            $pendingItems = $prescription->items()->where('status', 'Pending')->count();

            $newStatus = $pendingItems === 0 ? 'Dispensed' : 'Partially Dispensed';
            $prescription->update(['status' => $newStatus]);

            return $dispensing;
        });

        return redirect()->route('pharmacy.dispensing.show', $record)
                         ->with('success', 'Medication dispensed successfully and inventory batch logged.');
    }

    public function show(DispensingRecord $dispensing): View
    {
        $dispensing->load([
            'prescriptionItem.prescription.patient',
            'prescriptionItem.prescription.doctor',
            'pharmacist'
        ]);

        return view('pharmacy.dispensing.show', compact('dispensing'));
    }

    // Immutable records
    public function edit(DispensingRecord $dispensing): View { abort(403, 'Dispensing records cannot be edited once recorded.'); }
    public function update(Request $request, DispensingRecord $dispensing): RedirectResponse { abort(403); }
    public function destroy(DispensingRecord $dispensing): RedirectResponse { abort(403); }

    /** Print-friendly view for dispensing record. */
    public function print(DispensingRecord $dispensing): View
    {
        $dispensing->load([
            'prescriptionItem.prescription.patient',
            'prescriptionItem.prescription.doctor',
            'pharmacist'
        ]);

        return view('pharmacy.dispensing.print', compact('dispensing'));
    }
}
