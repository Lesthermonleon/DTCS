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
use Illuminate\View\View;

/**
 * DispensingController — pharmacist dispenses medications.
 */
class DispensingController extends Controller
{
    public function index(Request $request): View
    {
        $records = DispensingRecord::with('prescriptionItem.prescription.patient', 'pharmacist')
                   ->latest()->paginate(15);

        return view('pharmacy.dispensing.index', compact('records'));
    }

    public function create(): View
    {
        // Only show verified prescriptions with pending items
        $prescriptions = Prescription::where('status', 'Verified')
                         ->with('patient', 'items')
                         ->get();

        return view('pharmacy.dispensing.create', compact('prescriptions'));
    }

    public function store(StoreDispensingRecordRequest $request): RedirectResponse
    {
        $record = DispensingRecord::create(array_merge($request->validated(), [
            'pharmacist_id' => Auth::id(),
            'dispensed_at'  => now(),
        ]));

        // Mark item as dispensed
        $item = PrescriptionItem::find($request->prescription_item_id);
        $item->update(['status' => 'Dispensed']);

        // Check if all items are dispensed → update prescription status
        $prescription  = $item->prescription;
        $pendingItems  = $prescription->items()->where('status', 'Pending')->count();
        $newStatus     = $pendingItems === 0 ? 'Dispensed' : 'Partially Dispensed';
        $prescription->update(['status' => $newStatus]);

        return redirect()->route('pharmacy.dispensing.index')
                         ->with('success', 'Medication dispensed successfully.');
    }

    public function show(DispensingRecord $dispensing): View
    {
        $dispensing->load('prescriptionItem.prescription.patient', 'pharmacist');

        return view('pharmacy.dispensing.show', compact('dispensing'));
    }

    // Remaining CRUD stubs — dispensing records are generally immutable once created
    public function edit(DispensingRecord $dispensing): View    { abort(403); }
    public function update(Request $request, DispensingRecord $dispensing): RedirectResponse { abort(403); }
    public function destroy(DispensingRecord $dispensing): RedirectResponse { abort(403); }
}
