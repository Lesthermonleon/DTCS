<?php

namespace App\Http\Controllers\Diet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDietPlanRequest;
use App\Models\DietPlan;
use App\Models\DietRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * DietPlanController — dietitians create and manage therapeutic diet plans.
 */
class DietPlanController extends Controller
{
    public function index(Request $request): View
    {
        $plans = DietPlan::with('dietRequest.patient', 'dietitian')
                 ->latest()->paginate(15)->withQueryString();

        return view('diet.plans.index', compact('plans'));
    }

    public function create(): View
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('dietitian'), 403, 'Only dietitians can create therapeutic diet plans.');

        $pendingRequests = DietRequest::where('status', 'Pending')
                           ->whereDoesntHave('dietPlan')
                           ->with('patient')
                           ->get();

        return view('diet.plans.create', compact('pendingRequests'));
    }

    public function store(StoreDietPlanRequest $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('dietitian'), 403, 'Only dietitians can create therapeutic diet plans.');
        $request->validate(['diet_request_id' => 'required|exists:diet_requests,id']);

        $plan = DietPlan::create(array_merge($request->validated(), [
            'diet_request_id' => $request->diet_request_id,
            'dietitian_id'    => Auth::id(),
            'status'          => 'Active',
        ]));

        // Activate the diet request
        DietRequest::find($request->diet_request_id)->update(['status' => 'Active']);

        return redirect()->route('diet.plans.show', $plan)
                         ->with('success', 'Diet plan created successfully.');
    }

    public function show(DietPlan $dietPlan): View
    {
        $dietPlan->load('dietRequest.patient', 'dietRequest.doctor', 'dietitian', 'mealSchedules');

        return view('diet.plans.show', compact('dietPlan'));
    }

    public function edit(DietPlan $dietPlan): View
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('dietitian'), 403, 'Only dietitians can edit therapeutic diet plans.');
        abort_if($dietPlan->status === 'Completed', 403, 'Completed plans cannot be edited.');

        return view('diet.plans.edit', compact('dietPlan'));
    }

    public function update(StoreDietPlanRequest $request, DietPlan $dietPlan): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('dietitian'), 403, 'Only dietitians can edit therapeutic diet plans.');
        $dietPlan->update($request->validated());

        return redirect()->route('diet.plans.show', $dietPlan)
                         ->with('success', 'Diet plan updated.');
    }

    public function destroy(DietPlan $dietPlan): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('dietitian'), 403, 'Only dietitians can delete therapeutic diet plans.');
        abort_if($dietPlan->status === 'Completed', 403, 'Completed plans cannot be deleted.');
        $dietPlan->dietRequest->update(['status' => 'Pending']);
        $dietPlan->delete();

        return redirect()->route('diet.plans.index')
                         ->with('success', 'Diet plan removed.');
    }

    public function complete(DietPlan $dietPlan): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user?->hasRole('dietitian'), 403, 'Only dietitians can complete therapeutic diet plans.');
        $dietPlan->update(['status' => 'Completed']);
        $dietPlan->dietRequest->update(['status' => 'Completed']);

        return back()->with('success', 'Diet plan marked as completed.');
    }

    /** Print-friendly view for diet plan. */
    public function print(DietPlan $dietPlan): View
    {
        $dietPlan->load('dietRequest.patient', 'dietRequest.doctor', 'dietitian');

        return view('diet.plans.print', compact('dietPlan'));
    }
}
