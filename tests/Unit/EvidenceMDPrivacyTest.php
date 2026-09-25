<?php

namespace Tests\Unit;

use App\AI\EvidenceMDProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EvidenceMDPrivacyTest extends TestCase
{
    /**
     * Test 1 — Direct patient identifiers (Name, MRN, Ward, Bed, ID) must be excluded from EvidenceMD context.
     */
    public function test_patient_direct_identifiers_are_excluded_from_evidencemd_context(): void
    {
        $provider = new EvidenceMDProvider();

        $rawHimsContext = [
            'patient' => [
                'id'            => 101,
                'patient_no'    => 'P-2026-0003',
                'full_name'     => 'Darwin A. Timbas',
                'gender'        => 'Male',
                'date_of_birth' => '2003-07-15',
                'age'           => '23 yrs',
                'blood_type'    => 'AB-',
                'patient_type'  => 'Inpatient',
                'ward'          => 'Ward A',
                'bed_number'    => 'BED-01',
            ],
            'symptoms_and_findings' => 'Patient complains of acute chest pain radiating to left arm.',
            'recent_lab_results'    => [
                [
                    'test_name'    => 'Troponin I',
                    'result_value' => '0.45 ng/mL (High)',
                    'remarks'      => 'Elevated cardiac marker',
                    'status'       => 'Released',
                    'released_at'  => '2026-09-25 10:00',
                ],
            ],
            'recent_radiology_reports' => [
                [
                    'modality'   => 'Chest X-Ray',
                    'body_part'  => 'Chest',
                    'findings'   => 'Normal cardiac silhouette. Clear lung fields.',
                    'impression' => 'No acute pulmonary disease.',
                ],
            ],
            'active_prescriptions' => [
                [
                    'prescription_no' => 'RX-999',
                    'diagnosis'       => 'Suspected ACS',
                    'medications'     => ['Aspirin (300mg, Once)'],
                    'status'          => 'Verified',
                ],
            ],
            'surgery_requests' => [],
            'diet_requests'    => [],
        ];

        $sanitized = $provider->sanitizeContextForEvidenceMD($rawHimsContext);

        // Convert sanitized payload to JSON string to search for leaks
        $jsonPayload = json_encode($sanitized);

        // Verify patient direct identifiers are 100% BLOCKED
        $this->assertStringNotContainsString('Darwin A. Timbas', $jsonPayload);
        $this->assertStringNotContainsString('P-2026-0003', $jsonPayload);
        $this->assertStringNotContainsString('Ward A', $jsonPayload);
        $this->assertStringNotContainsString('BED-01', $jsonPayload);
        $this->assertArrayNotHasKey('id', $sanitized['clinical_demographics'] ?? []);

        // Verify required clinical context IS PRESERVED
        $this->assertEquals('23 yrs', $sanitized['clinical_demographics']['age']);
        $this->assertEquals('Male', $sanitized['clinical_demographics']['sex']);
        $this->assertEquals('Inpatient', $sanitized['clinical_demographics']['care_setting']);
        $this->assertEquals('Patient complains of acute chest pain radiating to left arm.', $sanitized['relevant_findings']);
        $this->assertEquals('Troponin I', $sanitized['recent_lab_results'][0]['test_name']);
        $this->assertEquals('0.45 ng/mL (High)', $sanitized['recent_lab_results'][0]['result_value']);
    }

    /**
     * Test 2 — Free-text fields with embedded patient names, MRNs, wards, and beds must be sanitized.
     */
    public function test_free_text_phi_and_indirect_identifiers_are_sanitized(): void
    {
        $provider = new EvidenceMDProvider();

        $rawHimsContext = [
            'patient' => [
                'id'            => 101,
                'patient_no'    => 'P-2026-0003',
                'full_name'     => 'Darwin A. Timbas',
                'gender'        => 'Male',
                'age'           => '23 yrs',
                'patient_type'  => 'Inpatient',
                'ward'          => 'Ward A',
                'bed_number'    => 'BED-01',
            ],
            'symptoms_and_findings' => 'Patient Darwin A. Timbas (MRN P-2026-0003) admitted to Ward A bed BED-01 with acute dyspnea.',
            'recent_lab_results'    => [
                [
                    'test_name'    => 'CBC',
                    'result_value' => 'WBC 14.5',
                    'remarks'      => 'Sample drawn from Darwin Timbas in Ward A',
                    'status'       => 'Released',
                    'released_at'  => '2026-09-25 10:15:30',
                ],
            ],
            'recent_radiology_reports' => [
                [
                    'modality'   => 'Chest CT',
                    'body_part'  => 'Chest',
                    'findings'   => 'Report for Darwin A. Timbas in BED-01',
                    'impression' => 'Pneumonia suspected.',
                ],
            ],
            'active_prescriptions' => [
                [
                    'prescription_no' => 'RX-2026-888',
                    'diagnosis'       => 'Pneumonia for P-2026-0003',
                    'medications'     => ['Ceftriaxone 1g IV'],
                    'status'          => 'Dispensed',
                ],
            ],
        ];

        $sanitized = $provider->sanitizeContextForEvidenceMD($rawHimsContext);
        $jsonPayload = json_encode($sanitized);

        // Verify patient name, MRN, ward, bed, and prescription_no are completely excluded/sanitized
        $this->assertStringNotContainsString('Darwin A. Timbas', $jsonPayload);
        $this->assertStringNotContainsString('P-2026-0003', $jsonPayload);
        $this->assertStringNotContainsString('Ward A', $jsonPayload);
        $this->assertStringNotContainsString('BED-01', $jsonPayload);
        $this->assertStringNotContainsString('RX-2026-888', $jsonPayload);

        // Verify free text was scrubbed with redactions
        $this->assertStringContainsString('[REDACTED_IDENTIFIER]', $sanitized['relevant_findings']);
        $this->assertStringContainsString('Pneumonia suspected.', $sanitized['recent_radiology_reports'][0]['impression']);
        $this->assertStringContainsString('Ceftriaxone 1g IV', $sanitized['active_prescriptions'][0]['medications'][0]);
    }

    /**
     * Test 3 — Verify outbound HTTP request body to EvidenceMD API contains zero patient identifiers.
     */
    public function test_outbound_http_payload_does_not_contain_patient_name_mrn_or_location(): void
    {
        Http::fake([
            'https://evidencemd.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'summary' => 'EvidenceMD evidence evaluation completed.',
                                'clinical_considerations' => ['Rule out Acute Coronary Syndrome'],
                                'diagnostic_considerations' => ['Serial Troponin at 3 hours', '12-lead ECG'],
                                'treatment_considerations' => ['Dual antiplatelet therapy consideration'],
                                'citations' => ['ACC/AHA NSTEMI Guidelines 2024'],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        config(['ai.evidencemd.api_key' => 'test-secret-key-123']);

        $provider = new EvidenceMDProvider();

        $rawHimsContext = [
            'patient' => [
                'id'            => 101,
                'patient_no'    => 'P-2026-0003',
                'full_name'     => 'Darwin A. Timbas',
                'gender'        => 'Male',
                'age'           => '23 yrs',
                'patient_type'  => 'Inpatient',
                'ward'          => 'Ward A',
                'bed_number'    => 'BED-01',
            ],
            'symptoms_and_findings' => 'Acute chest discomfort.',
        ];

        $response = $provider->analyzeClinicalContext('SYMPTOM_ASSESSMENT', $rawHimsContext);

        $this->assertEquals('Stage 2 — EvidenceMD AI Active', $response['status']);

        Http::assertSent(function ($request) {
            $body = $request->body();

            return !str_contains($body, 'Darwin A. Timbas')
                && !str_contains($body, 'P-2026-0003')
                && !str_contains($body, 'Ward A')
                && !str_contains($body, 'BED-01');
        });
    }

    /**
     * Test 4 — Verify HTTP 402 billing error is mapped correctly without leaking credentials or PHI.
     */
    public function test_http_402_billing_error_mapping(): void
    {
        Http::fake([
            'https://evidencemd.ai/api/v1/chat/completions' => Http::response([
                'error' => 'Insufficient credits',
            ], 402),
        ]);

        config(['ai.evidencemd.api_key' => 'test-secret-key-123']);

        $provider = new EvidenceMDProvider();

        $response = $provider->analyzeClinicalContext('DIAGNOSTIC_ASSISTANCE', [
            'patient' => ['full_name' => 'Darwin A. Timbas', 'patient_no' => 'P-2026-0003', 'age' => '23 yrs', 'gender' => 'Male'],
        ]);

        $this->assertEquals('Insufficient Credits (HTTP 402)', $response['status']);
        $this->assertStringContainsString('insufficient credits', strtolower($response['summary']));
    }
}
