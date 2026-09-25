<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\MediSense\ClinicalContextBuilder;
use App\Services\MediSense\MedisenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MedisenseController — Doctor-only Clinical Decision Support Controller.
 *
 * Backend access is strictly restricted to Doctor users via 'role:doctor' middleware.
 * Non-doctor roles (Admin, Med-Tech, Rad-Tech, Radiologist, Pharmacist, Dietitian, OR Coordinator)
 * will receive HTTP 403 Forbidden.
 */
class MedisenseController extends Controller
{
    protected MedisenseService $medisenseService;
    protected ClinicalContextBuilder $contextBuilder;

    public function __construct(MedisenseService $medisenseService, ClinicalContextBuilder $contextBuilder)
    {
        $this->medisenseService = $medisenseService;
        $this->contextBuilder   = $contextBuilder;
    }

    /**
     * Display Patient Directory for MediSense Clinical Analysis selection.
     */
    public function index(Request $request): View
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_no', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('ward', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('patient_type', $request->type);
        }

        $patients = $query
            ->select(['id','patient_no','first_name','middle_name','last_name','gender','patient_type','ward','bed_number'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('medisense.index', compact('patients'));
    }

    /**
     * Display MediSense AI workspace for a specific patient.
     */
    public function show(Patient $patient): View
    {
        $context = $this->contextBuilder->build($patient);

        return view('medisense.show', compact('patient', 'context'));
    }

    /**
     * Save/update Doctor clinical assessment findings for the patient.
     */
    public function updateFindings(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'clinical_findings' => 'nullable|string|max:5000',
        ]);

        $patient->update([
            'clinical_findings' => $validated['clinical_findings'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'           => true,
                'message'           => 'Clinical findings updated successfully.',
                'clinical_findings' => $patient->clinical_findings,
            ]);
        }

        return redirect()->route('medisense.show', $patient)->with('success', 'Clinical assessment findings updated successfully.');
    }

    /**
     * Execute a specific MediSense clinical decision-support task.
     */
    public function analyze(Request $request, Patient $patient): JsonResponse
    {
        $validated = $request->validate([
            'task' => 'required|string|in:SYMPTOM_ASSESSMENT,DIAGNOSTIC_ASSISTANCE,TREATMENT_RECOMMENDATION,CLINICAL_SERVICE_ASSISTANCE',
        ]);

        try {
            $result = $this->medisenseService->executeTask($validated['task'], $patient);

            $category = $result['category'] ?? null;
            $isError  = !empty($category) && $category !== 'active';

            if ($isError) {
                $httpStatus = ($category === 'insufficient_credits') ? 402 : 400;

                return response()->json([
                    'success'  => false,
                    'category' => $category,
                    'message'  => $result['summary'] ?? 'MediSense analysis error.',
                    'data'     => $result,
                ], $httpStatus);
            }

            return response()->json([
                'success'  => true,
                'category' => 'active',
                'data'     => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success'  => false,
                'category' => 'exception',
                'message'  => 'MediSense analysis error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
