<?php

namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRadiologyRequestRequest;
use App\Models\RadiologyImage;
use App\Models\RadiologyRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RadiologyRequestController — manages radiology imaging requests.
 */
class RadiologyRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = RadiologyRequest::with('patient', 'doctor');

        if (auth()->user()->hasRole('doctor')) {
            $query->where('doctor_id', auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                  ->orWhere('modality', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('first_name', 'like', "%{$search}%")
                                                       ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        if ($s = $request->get('status'))   { $query->where('status', $s); }
        if ($m = $request->get('modality')) { $query->where('modality', $m); }

        $radiologyRequests = $query->latest()->paginate(15)->withQueryString();
        $modalities = ['X-Ray', 'CT Scan', 'MRI', 'Ultrasound', 'Mammography', 'Fluoroscopy'];
        $statuses   = ['Pending', 'Scheduled', 'In Progress', 'Completed', 'Cancelled'];

        return view('radiology.requests.index', compact('radiologyRequests', 'modalities', 'statuses'));
    }

    public function create(): View
    {
        $patients   = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $modalities = ['X-Ray', 'CT Scan', 'MRI', 'Ultrasound', 'Mammography', 'Fluoroscopy'];

        return view('radiology.requests.create', compact('patients', 'modalities'));
    }

    public function store(StoreRadiologyRequestRequest $request): RedirectResponse
    {
        $count = RadiologyRequest::count() + 1;

        RadiologyRequest::create(array_merge($request->validated(), [
            'request_no' => 'RR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
            'doctor_id'  => auth()->id(),
            'status'     => 'Pending',
            'requested_at' => now(),
        ]));

        return redirect()->route('radiology.requests.index')
                         ->with('success', 'Radiology request created successfully.');
    }

    public function show(RadiologyRequest $radiologyRequest): View
    {
        $radiologyRequest->load('patient', 'doctor', 'images.uploadedBy', 'report.radiologist');

        return view('radiology.requests.show', compact('radiologyRequest'));
    }

    public function edit(RadiologyRequest $radiologyRequest): View
    {
        abort_if($radiologyRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');
        $patients   = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $modalities = ['X-Ray', 'CT Scan', 'MRI', 'Ultrasound', 'Mammography', 'Fluoroscopy'];

        return view('radiology.requests.edit', compact('radiologyRequest', 'patients', 'modalities'));
    }

    public function update(StoreRadiologyRequestRequest $request, RadiologyRequest $radiologyRequest): RedirectResponse
    {
        abort_if($radiologyRequest->status !== 'Pending', 403, 'Only pending requests can be edited.');
        $radiologyRequest->update($request->validated());

        return redirect()->route('radiology.requests.show', $radiologyRequest)
                         ->with('success', 'Radiology request updated.');
    }

    public function destroy(RadiologyRequest $radiologyRequest): RedirectResponse
    {
        abort_if($radiologyRequest->status !== 'Pending', 403, 'Only pending requests can be cancelled.');
        $radiologyRequest->update(['status' => 'Cancelled']);

        return redirect()->route('radiology.requests.index')
                         ->with('success', 'Radiology request cancelled.');
    }

    public function schedule(RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $radiologyRequest->update(['status' => 'Scheduled', 'scheduled_at' => now()]);

        return back()->with('success', 'Request marked as scheduled.');
    }

    public function uploadImage(Request $request, RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $request->validate(['image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:20480']);

        $file = $request->file('image');
        $path = $file->store("radiology/{$radiologyRequest->id}", 'public');

        RadiologyImage::create([
            'radiology_request_id' => $radiologyRequest->id,
            'file_path'  => $path,
            'file_name'  => $file->getClientOriginalName(),
            'file_type'  => $file->getClientOriginalExtension(),
            'file_size'  => $file->getSize(),
            'uploaded_by'=> auth()->id(),
            'uploaded_at'=> now(),
        ]);

        // Mark request as in-progress
        $radiologyRequest->update(['status' => 'In Progress']);

        return back()->with('success', 'Image uploaded successfully.');
    }
}
