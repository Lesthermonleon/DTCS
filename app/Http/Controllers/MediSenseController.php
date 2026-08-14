<?php

namespace App\Http\Controllers;

use App\Models\MediSenseInteraction;
use App\Models\Patient;
use App\Services\VirtualMediSenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MediSenseController — Controls workspace views and API interactions for Virtual MediSense AI.
 */
class MediSenseController extends Controller
{
    protected VirtualMediSenseService $aiService;

    public function __construct(VirtualMediSenseService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display the dedicated Virtual MediSense AI workspace.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $capabilities = $this->aiService->getAllowedCapabilities($user);

        // Fetch patient list for patient selection dropdown if authorized
        $patients = collect();
        if ($user->canAccessPatients() || in_array($user->primaryRole, ['med-tech', 'rad-tech', 'radiologist', 'pharmacist', 'dietitian', 'or-coordinator'])) {
            $patients = Patient::select('id', 'patient_no', 'first_name', 'last_name', 'gender', 'ward')
                ->latest()
                ->take(100)
                ->get();
        }

        // Active selected patient ID if passed via query parameter (e.g. ?patient_id=5)
        $selectedPatientId = $request->query('patient_id');
        $selectedPatient = $selectedPatientId ? Patient::find($selectedPatientId) : null;

        // Recent history for this user
        $history = MediSenseInteraction::with('patient')
            ->where('user_id', $user->id)
            ->latest()
            ->take(15)
            ->get();

        return view('medisense.index', compact(
            'user',
            'capabilities',
            'patients',
            'selectedPatient',
            'history'
        ));
    }

    /**
     * Get JSON list of allowed capabilities for the authenticated user (used by FAB/widget).
     */
    public function capabilities(Request $request): JsonResponse
    {
        $capabilities = $this->aiService->getAllowedCapabilities($request->user());

        return response()->json([
            'success'      => true,
            'role'         => $request->user()->primaryRole,
            'role_name'    => $request->user()->roleName,
            'capabilities' => array_values($capabilities),
        ]);
    }

    /**
     * Handle AI chat request from dedicated page or floating assistant.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'capability'           => 'nullable|string|max:100',
            'prompt'               => 'required|string|max:3000',
            'patient_id'           => 'nullable|integer|exists:patients,id',
            'module'               => 'nullable|string|max:50',
            'custom_notes'         => 'nullable|string|max:2000',
            'conversation_history' => 'nullable|array',
        ]);

        $user = $request->user();
        $capability = $request->input('capability');
        $prompt = $request->input('prompt');
        $patientId = $request->input('patient_id');

        $additionalContext = [
            'module'               => $request->input('module'),
            'custom_input'         => $request->input('custom_notes'),
            'conversation_history' => $request->input('conversation_history', []),
        ];

        $result = $this->aiService->processRequest($user, $capability, $prompt, $patientId, $additionalContext);

        if (! $result['success']) {
            return response()->json($result, $result['code'] ?? 400);
        }

        return response()->json($result);
    }

    /**
     * Get user's interaction history (AJAX).
     */
    public function history(Request $request): JsonResponse
    {
        $history = MediSenseInteraction::with('patient')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'capability'    => $item->capability,
                    'user_prompt'   => $item->user_prompt,
                    'ai_response'   => $item->ai_response,
                    'created_at'    => $item->created_at->format('M d, Y h:i A'),
                    'patient_name'  => $item->patient ? $item->patient->full_name : null,
                    'patient_no'    => $item->patient ? $item->patient->patient_no : null,
                    'status'        => $item->status,
                ];
            });

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }

    /**
     * Clear current user's AI chat session logs.
     */
    public function clear(Request $request): JsonResponse
    {
        MediSenseInteraction::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'MediSense conversation history cleared.',
        ]);
    }
}
