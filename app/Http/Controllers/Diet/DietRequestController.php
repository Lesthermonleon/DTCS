<?php

namespace App\Http\Controllers\Diet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDietRequestRequest;
use App\Models\DietRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * DietRequestController — doctors request therapeutic diets for patients.
 */
class DietRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = DietRequest::with('patient', 'doctor', 'dietPlan');

        if (auth()->user()->hasRole('doctor')) {
            $query->where('doctor_id', auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                  ->orWhere('diet_type', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('last_name', 'like', "%{$search}%"));
            });
        }

        if ($s = $request->get('status')) { $query->where('status', $s); }

        $dietRequests = $query->latest()->paginate(15)->withQueryString();
        $statuses = ['Pending', 'Active', 'Completed', 'Cancelled'];

        return view('diet.requests.index', compact('dietRequests', 'statuses'));
    }

    public function create(): View
    {
        $patients  = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $dietTypes = ['Diabetic', 'Low-Sodium', 'Renal', 'Cardiac', 'High-Protein', 'Low-Fat', 'Liquid', 'Soft', 'Regular'];

        return view('diet.requests.create', compact('patients', 'dietTypes'));
    }

    public function store(StoreDietRequestRequest $request): RedirectResponse
    {
        $count = DietRequest::count() + 1;

        DietRequest::create(array_merge($request->validated(), [
            'request_no'   => 'DR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
            'doctor_id'    => auth()->id(),
            'status'       => 'Pending',
            'requested_at' => now(),
        ]));

        return redirect()->route('diet.requests.index')
                         ->with('success', 'Diet request submitted successfully.');
    }

    public function show(DietRequest $dietRequest): View
    {
        $dietRequest->load('patient', 'doctor', 'dietPlan.dietitian', 'dietPlan.mealSchedules');

        return view('diet.requests.show', compact('dietRequest'));
    }

    public function edit(DietRequest $dietRequest): View
    {
        abort_if($dietRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');
        $patients  = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $dietTypes = ['Diabetic', 'Low-Sodium', 'Renal', 'Cardiac', 'High-Protein', 'Low-Fat', 'Liquid', 'Soft', 'Regular'];

        return view('diet.requests.edit', compact('dietRequest', 'patients', 'dietTypes'));
    }

    public function update(StoreDietRequestRequest $request, DietRequest $dietRequest): RedirectResponse
    {
        abort_if($dietRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');
        $dietRequest->update($request->validated());

        return redirect()->route('diet.requests.show', $dietRequest)
                         ->with('success', 'Diet request updated.');
    }

    public function destroy(DietRequest $dietRequest): RedirectResponse
    {
        abort_if($dietRequest->status !== 'Pending', 403, 'Only pending requests can be cancelled.');
        $dietRequest->update(['status' => 'Cancelled']);

        return redirect()->route('diet.requests.index')
                         ->with('success', 'Diet request cancelled.');
    }
}
