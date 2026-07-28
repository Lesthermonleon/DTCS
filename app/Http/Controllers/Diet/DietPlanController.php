<?php

namespace App\Http\Controllers\Diet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDietPlanRequest;
use App\Models\DietPlan;
use App\Models\DietRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $pendingRequests = DietRequest::where('status', 'Pending')
                           ->whereDoesntHave('dietPlan')
                           ->with('patient')
                           ->get();

        return view('diet.plans.create', compact('pendingRequests'));
    }

    public function store(StoreDietPlanRequest $request): RedirectResponse
    {
        $request->validate(['diet_request_id' => 'required|exists:diet_requests,id']);

        $plan = DietPlan::create(array_merge($request->validated(), [
            'diet_request_id' => $request->diet_request_id,
            'dietitian_id'    => auth()->id(),
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
        abort_if($dietPlan->status === 'Completed', 403, 'Completed plans cannot be edited.');

        return view('diet.plans.edit', compact('dietPlan'));
    }

    public function update(StoreDietPlanRequest $request, DietPlan $dietPlan): RedirectResponse
    {
        $dietPlan->update($request->validated());

        return redirect()->route('diet.plans.show', $dietPlan)
                         ->with('success', 'Diet plan updated.');
    }

    public function destroy(DietPlan $dietPlan): RedirectResponse
    {
        abort_if($dietPlan->status === 'Completed', 403, 'Completed plans cannot be deleted.');
        $dietPlan->dietRequest->update(['status' => 'Pending']);
        $dietPlan->delete();

        return redirect()->route('diet.plans.index')
                         ->with('success', 'Diet plan removed.');
    }

    public function complete(DietPlan $dietPlan): RedirectResponse
    {
        $dietPlan->update(['status' => 'Completed']);
        $dietPlan->dietRequest->update(['status' => 'Completed']);

        return back()->with('success', 'Diet plan marked as completed.');
    }
}
