<?php

namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRadiologyRequestRequest;
use App\Models\RadiologyImage;
use App\Models\RadiologyRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * RadiologyRequestController — manages radiology imaging requests & technologist procedure workflow.
 */
class RadiologyRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', RadiologyRequest::class);

        $query = RadiologyRequest::with('patient', 'doctor', 'report', 'images');

        /** @var User|null $user */
        $user = Auth::user();
        if ($user && $user->hasRole('doctor')) {
            $query->where('doctor_id', $user->id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                  ->orWhere('modality', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('first_name', 'like', "%{$search}%")
                                                       ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        if ($s = $request->input('status'))   { $query->where('status', $s); }
        if ($m = $request->input('modality')) { $query->where('modality', $m); }

        $radiologyRequests = $query->latest()->paginate(15)->withQueryString();
        $modalities = ['X-Ray', 'CT Scan', 'MRI', 'Ultrasound', 'Mammography', 'Fluoroscopy'];
        $statuses   = ['Pending', 'Scheduled', 'In Progress', 'Completed', 'Cancelled'];

        return view('radiology.requests.index', compact('radiologyRequests', 'modalities', 'statuses'));
    }

    public function create(): View
    {
        $this->authorize('create', RadiologyRequest::class);

        $patients   = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $modalities = ['X-Ray', 'CT Scan', 'MRI', 'Ultrasound', 'Mammography', 'Fluoroscopy'];

        return view('radiology.requests.create', compact('patients', 'modalities'));
    }

    public function store(StoreRadiologyRequestRequest $request): RedirectResponse
    {
        $this->authorize('create', RadiologyRequest::class);

        $count = RadiologyRequest::count() + 1;

        RadiologyRequest::create(array_merge($request->validated(), [
            'request_no' => 'RR-' . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT),
            'doctor_id'  => Auth::id(),
            'status'     => 'Pending',
            'requested_at' => now(),
        ]));

        return redirect()->route('radiology.requests.index')
                         ->with('success', 'Radiology request created successfully.');
    }

    public function show(RadiologyRequest $radiologyRequest): View
    {
        $this->authorize('view', $radiologyRequest);

        $radiologyRequest->load('patient', 'doctor', 'images.uploadedBy', 'report.radiologist');

        return view('radiology.requests.show', compact('radiologyRequest'));
    }

    public function edit(RadiologyRequest $radiologyRequest): View
    {
        $this->authorize('update', $radiologyRequest);

        $patients   = Patient::orderBy('last_name')->get(['id', 'patient_no', 'first_name', 'last_name']);
        $modalities = ['X-Ray', 'CT Scan', 'MRI', 'Ultrasound', 'Mammography', 'Fluoroscopy'];

        return view('radiology.requests.edit', compact('radiologyRequest', 'patients', 'modalities'));
    }

    public function update(StoreRadiologyRequestRequest $request, RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $this->authorize('update', $radiologyRequest);

        $radiologyRequest->update($request->validated());

        return redirect()->route('radiology.requests.show', $radiologyRequest)
                         ->with('success', 'Radiology request updated.');
    }

    public function destroy(RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $this->authorize('delete', $radiologyRequest);

        $radiologyRequest->update(['status' => 'Cancelled']);

        return redirect()->route('radiology.requests.index')
                         ->with('success', 'Radiology request cancelled.');
    }

    public function schedule(RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $this->authorize('schedule', $radiologyRequest);

        $radiologyRequest->update(['status' => 'Scheduled', 'scheduled_at' => now()]);

        return back()->with('success', 'Request marked as scheduled.');
    }

    public function start(RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $this->authorize('start', $radiologyRequest);

        $radiologyRequest->update(['status' => 'In Progress']);

        return back()->with('success', 'Imaging procedure started.');
    }

    public function uploadImage(Request $request, RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $this->authorize('uploadImage', $radiologyRequest);

        $maxKb    = (int) config('radiology.max_upload_size_kb', 20480);
        $maxFiles = (int) config('radiology.max_files_per_upload', 10);

        // Retrieve raw uploaded file arrays directly from request (before isValidFile checks)
        $rawImages    = $request->file('images') ? (array) $request->file('images') : [];
        $rawImage     = $request->file('image') ? [$request->file('image')] : [];
        $rawDocuments = $request->file('documents') ? (array) $request->file('documents') : [];

        // Filter out empty file slots (UPLOAD_ERR_NO_FILE) to check if the user selected ANY file
        $userSelectedImages = array_values(array_filter(array_merge($rawImages, $rawImage), function ($f) {
            return $f instanceof \Illuminate\Http\UploadedFile && $f->getError() !== UPLOAD_ERR_NO_FILE;
        }));

        $userSelectedDocuments = array_values(array_filter($rawDocuments, function ($f) {
            return $f instanceof \Illuminate\Http\UploadedFile && $f->getError() !== UPLOAD_ERR_NO_FILE;
        }));

        $userSelectedAnyFiles = !empty($userSelectedImages) || !empty($userSelectedDocuments);
        $hasExistingImages    = $radiologyRequest->images()->count() > 0;
        $shouldComplete       = $request->input('action') === 'upload_complete' || $request->boolean('complete_procedure');

        // Only show fallback error if the user TRULY selected no files AND there are no pre-existing scans
        if (!$userSelectedAnyFiles && !$hasExistingImages) {
            return back()->withErrors(['images' => 'Please select at least one valid imaging scan or document file to upload.']);
        }

        if ($userSelectedAnyFiles) {
            $request->validate([
                'notes'  => 'nullable|string|max:1000',
                'action' => 'nullable|string|in:upload_only,upload_complete',
            ]);

            // Validate imaging files (DICOM, JPEG, PNG, WEBP)
            $allowedImagingExts = ['dcm', 'dicom', 'jpg', 'jpeg', 'png', 'webp', 'gif'];
            foreach ($userSelectedImages as $index => $file) {
                if (!$file instanceof \Illuminate\Http\UploadedFile) {
                    return back()->withErrors(["images.{$index}" => 'The selected file is invalid.']);
                }

                $err = $file->getError();
                if ($err !== UPLOAD_ERR_OK) {
                    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                        return back()->withErrors(["images.{$index}" => "The selected file could not be uploaded because it exceeds the server's configured upload limit (PHP upload error code {$err})."]);
                    }
                    if ($err === UPLOAD_ERR_PARTIAL) {
                        return back()->withErrors(["images.{$index}" => "The file upload was interrupted during transfer (PHP upload error code {$err}). Please try uploading again."]);
                    }
                    if ($err === UPLOAD_ERR_NO_TMP_DIR || $err === UPLOAD_ERR_CANT_WRITE) {
                        return back()->withErrors(["images.{$index}" => "Server storage error: unable to write temporary upload file (PHP upload error code {$err}). Please contact system administrator."]);
                    }
                    return back()->withErrors(["images.{$index}" => "The selected file failed to upload (PHP upload error code {$err})."]);
                }

                if ($file->getSize() > ($maxKb * 1024)) {
                    return back()->withErrors(["images.{$index}" => "The selected file could not be uploaded because it exceeds the server's configured upload limit."]);
                }

                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, $allowedImagingExts)) {
                    return back()->withErrors(["images.{$index}" => 'The selected file type is not supported.']);
                }
            }

            // Validate supporting documents (PDF)
            foreach ($userSelectedDocuments as $index => $file) {
                if (!$file instanceof \Illuminate\Http\UploadedFile) {
                    return back()->withErrors(["documents.{$index}" => 'The selected document is invalid.']);
                }

                $err = $file->getError();
                if ($err !== UPLOAD_ERR_OK) {
                    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                        return back()->withErrors(["documents.{$index}" => "The selected document could not be uploaded because it exceeds the server's configured upload limit (PHP upload error code {$err})."]);
                    }
                    if ($err === UPLOAD_ERR_PARTIAL) {
                        return back()->withErrors(["documents.{$index}" => "The file upload was interrupted during transfer (PHP upload error code {$err}). Please try uploading again."]);
                    }
                    if ($err === UPLOAD_ERR_NO_TMP_DIR || $err === UPLOAD_ERR_CANT_WRITE) {
                        return back()->withErrors(["documents.{$index}" => "Server storage error: unable to write temporary upload file (PHP upload error code {$err}). Please contact system administrator."]);
                    }
                    return back()->withErrors(["documents.{$index}" => "The selected document failed to upload (PHP upload error code {$err})."]);
                }

                if ($file->getSize() > ($maxKb * 1024)) {
                    return back()->withErrors(["documents.{$index}" => "The selected document could not be uploaded because it exceeds the server's configured upload limit."]);
                }

                $ext = strtolower($file->getClientOriginalExtension());
                if ($ext !== 'pdf') {
                    return back()->withErrors(["documents.{$index}" => 'Supporting documents must be in PDF format.']);
                }
            }
        }

        $allUploadedFiles = array_merge($userSelectedImages, $userSelectedDocuments);
        $count = 0;

        foreach ($allUploadedFiles as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension());
            if ($ext === 'dicom') {
                $ext = 'dcm';
            }

            $path = $file->store("radiology/{$radiologyRequest->id}", 'public');

            RadiologyImage::create([
                'radiology_request_id' => $radiologyRequest->id,
                'file_path'            => $path,
                'file_name'            => $file->getClientOriginalName(),
                'file_type'            => $ext,
                'file_size'            => $file->getSize(),
                'uploaded_by'          => Auth::id(),
                'uploaded_at'          => now(),
                'notes'                => $request->input('notes'),
            ]);
            $count++;
        }

        if ($shouldComplete) {
            $radiologyRequest->update([
                'status'       => 'Completed',
                'completed_at' => now(),
            ]);
            $message = $count > 0
                ? "{$count} study file(s) uploaded and procedure completed! Study is now ready for Radiologist interpretation."
                : "Procedure marked as COMPLETED! Study is now ready for Radiologist interpretation.";
        } else {
            if (in_array($radiologyRequest->status, ['Scheduled', 'Pending'])) {
                $radiologyRequest->update(['status' => 'In Progress']);
            }
            $message = "{$count} study file(s) uploaded successfully.";
        }

        return back()->with('success', $message);
    }

    public function viewImage(RadiologyImage $image)
    {
        $this->authorize('view', $image->radiologyRequest);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (!$disk->exists($image->file_path)) {
            abort(404, 'Imaging file not found on disk.');
        }

        $filePath = $disk->path($image->file_path);

        $mimeType = match (strtolower($image->file_type)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'        => 'image/png',
            'webp'       => 'image/webp',
            'gif'        => 'image/gif',
            'pdf'        => 'application/pdf',
            default      => $disk->mimeType($image->file_path) ?: 'application/octet-stream',
        };

        return response()->file($filePath, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($image->file_name) . '"',
        ]);
    }

    public function complete(RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $this->authorize('complete', $radiologyRequest);

        $radiologyRequest->update([
            'status'       => 'Completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Imaging procedure completed and sent for interpretation.');
    }
}
