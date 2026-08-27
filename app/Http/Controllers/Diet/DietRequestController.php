<?php

namespace App\Http\Controllers\Diet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDietRequestRequest;
use App\Models\ActivityLog;
use App\Models\DietRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * DietRequestController — doctors request therapeutic diets for patients.
 */
class DietRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = DietRequest::with('patient', 'doctor', 'dietPlan');

        /** @var User|null $user */
        $user = Auth::user();

        if ($user?->hasRole('doctor')) {
            $query->where('doctor_id', Auth::id());
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                  ->orWhere('diet_type', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('last_name', 'like', "%{$search}%"));
            });
        }

        if ($s = $request->input('status')) { $query->where('status', $s); }

        $dietRequests = $query->latest()->paginate(15)->withQueryString();
        $statuses = ['Pending', 'Active', 'Completed', 'Cancelled'];

        return view('diet.requests.index', compact('dietRequests', 'statuses'));
    }

    public function create(): View
    {
        // Only Doctors may originate therapeutic diet requests
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can create diet requests.');

        $patients  = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $dietTypes = ['Diabetic', 'Low-Sodium', 'Renal', 'Cardiac', 'High-Protein', 'Low-Fat', 'Liquid', 'Soft', 'Regular'];

        return view('diet.requests.create', compact('patients', 'dietTypes'));
    }

    public function store(StoreDietRequestRequest $request): RedirectResponse
    {
        // Only Doctors may originate therapeutic diet requests
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('doctor'), 403, 'Only physicians can create diet requests.');

        $count = DietRequest::count() + 1;

        DietRequest::create(array_merge($request->validated(), [
            'request_no'   => 'DR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
            'doctor_id'    => Auth::id(),
            'status'       => 'Pending',
            'requested_at' => now(),
        ]));

        $latest = DietRequest::where('doctor_id', Auth::id())->latest()->first();
        if ($latest) {
            ActivityLog::create([
                'user_id'       => Auth::id(),
                'action'        => 'Diet Request Created',
                'module'        => 'Diet & Nutrition',
                'description'   => "Diet request [{$latest->request_no}] ({$latest->diet_type}) was submitted.",
                'loggable_type' => DietRequest::class,
                'loggable_id'   => $latest->id,
                'ip_address'    => request()->ip(),
                'logged_at'     => now(),
            ]);
        }

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

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Diet Request Updated',
            'module'        => 'Diet & Nutrition',
            'description'   => "Diet request [{$dietRequest->request_no}] was updated.",
            'loggable_type' => DietRequest::class,
            'loggable_id'   => $dietRequest->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('diet.requests.show', $dietRequest)
                         ->with('success', 'Diet request updated.');
    }

    public function destroy(DietRequest $dietRequest): RedirectResponse
    {
        abort_if($dietRequest->status !== 'Pending', 403, 'Only pending requests can be cancelled.');
        $dietRequest->update(['status' => 'Cancelled']);

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Diet Request Cancelled',
            'module'        => 'Diet & Nutrition',
            'description'   => "Diet request [{$dietRequest->request_no}] was cancelled.",
            'loggable_type' => DietRequest::class,
            'loggable_id'   => $dietRequest->id,
            'ip_address'    => request()->ip(),
            'logged_at'     => now(),
        ]);

        return redirect()->route('diet.requests.index')
                         ->with('success', 'Diet request cancelled.');
    }
}
