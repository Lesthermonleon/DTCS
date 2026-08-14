<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Virtual MediSense AI Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('GEMINI_API_KEY') ?? env('MEDISENSE_API_KEY', ''),
    'api_url' => env('GEMINI_API_URL') ?? env('MEDISENSE_API_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    'model'   => env('GEMINI_MODEL') ?? env('MEDISENSE_MODEL', 'gemini-pro-latest'),
    'timeout' => (int) (env('GEMINI_TIMEOUT') ?? env('MEDISENSE_TIMEOUT', 30)),
    'enable_web_search' => (bool) (env('GEMINI_ENABLE_WEB_SEARCH') ?? env('MEDISENSE_ENABLE_WEB_SEARCH', true)),

    /*
    |--------------------------------------------------------------------------
    | Role-Based AI Capability Matrix
    |--------------------------------------------------------------------------
    | Defines allowed capabilities per user role slug.
    */
    'capabilities' => [
        'doctor' => [
            'symptom_assessment' => [
                'label' => 'Symptom Assessment',
                'icon'  => 'bi-activity',
                'desc'  => 'Analyze reported patient symptoms and present possible clinical considerations.',
            ],
            'diagnostic_assistance' => [
                'label' => 'Diagnostic Assistance',
                'icon'  => 'bi-search-heart',
                'desc'  => 'Review clinical data and suggest potential diagnostic evaluations.',
            ],
            'treatment_recommendation' => [
                'label' => 'Treatment Recommendation',
                'icon'  => 'bi-prescription',
                'desc'  => 'Generate decision-support recommendations for clinical treatment plans.',
            ],
            'clinical_summary' => [
                'label' => 'Clinical Summary',
                'icon'  => 'bi-file-earmark-medical',
                'desc'  => 'Synthesize overall patient record into a concise clinical briefing.',
            ],
            'laboratory_analysis' => [
                'label' => 'Laboratory Result Summary',
                'icon'  => 'bi-clipboard2-pulse',
                'desc'  => 'Summarize and interpret abnormal laboratory test parameters.',
            ],
            'imaging_summary' => [
                'label' => 'Imaging Report Summary',
                'icon'  => 'bi-file-text',
                'desc'  => 'Summarize radiology findings and key imaging observations.',
            ],
            'medication_information' => [
                'label' => 'Medication Information',
                'icon'  => 'bi-capsule',
                'desc'  => 'Review dosing, indications, contraindications, and drug interactions.',
            ],
        ],

        'med-tech' => [
            'laboratory_assistance' => [
                'label' => 'Laboratory Result Assistance',
                'icon'  => 'bi-clipboard2-pulse',
                'desc'  => 'Assist with laboratory reference ranges, unit conversions, and analyte values.',
            ],
            'laboratory_summary' => [
                'label' => 'Laboratory Result Summary',
                'icon'  => 'bi-journal-medical',
                'desc'  => 'Summarize pending and processed laboratory test batches.',
            ],
            'test_information' => [
                'label' => 'Laboratory Test Information',
                'icon'  => 'bi-info-circle',
                'desc'  => 'Look up test specimen requirements, stability, and handling instructions.',
            ],
        ],

        'rad-tech' => [
            'imaging_workflow' => [
                'label' => 'Imaging Procedure Assistance',
                'icon'  => 'bi-camera',
                'desc'  => 'Guidance on radiologic positioning, technical protocols, and contrast safety.',
            ],
            'radiology_workflow' => [
                'label' => 'Radiology Workflow Assistance',
                'icon'  => 'bi-card-checklist',
                'desc'  => 'Track pending imaging modalities and schedule prioritization.',
            ],
        ],

        'radiologist' => [
            'imaging_summary' => [
                'label' => 'Imaging Report Assistance',
                'icon'  => 'bi-file-earmark-text',
                'desc'  => 'Assist in structuring radiology reports and standardized impressions.',
            ],
            'radiology_findings' => [
                'label' => 'Radiology Findings Summary',
                'icon'  => 'bi-journal-text',
                'desc'  => 'Summarize key radiologic imaging patterns and differential considerations.',
            ],
            'radiology_assistance' => [
                'label' => 'Radiology Decision Support',
                'icon'  => 'bi-search',
                'desc'  => 'Lookup specialized radiologic terminology and classification criteria.',
            ],
        ],

        'pharmacist' => [
            'medication_information' => [
                'label' => 'Medication Information',
                'icon'  => 'bi-capsule',
                'desc'  => 'Comprehensive drug monographs, pharmacology, and indications.',
            ],
            'prescription_assistance' => [
                'label' => 'Prescription Assistance',
                'icon'  => 'bi-prescription2',
                'desc'  => 'Review dosage regimens, renal adjustments, and administration safety.',
            ],
            'medication_review' => [
                'label' => 'Medication Review',
                'icon'  => 'bi-check2-circle',
                'desc'  => 'Assess polypharmacy, interactions, and therapeutic duplications.',
            ],
        ],

        'dietitian' => [
            'nutrition_assessment' => [
                'label' => 'Nutrition Assessment',
                'icon'  => 'bi-apple',
                'desc'  => 'Evaluate nutritional requirements, caloric target calculations, and deficiencies.',
            ],
            'diet_plan_assistance' => [
                'label' => 'Therapeutic Diet Assistance',
                'icon'  => 'bi-clipboard2-heart',
                'desc'  => 'Formulate clinical diet plans tailored to renal, diabetic, or cardiac conditions.',
            ],
        ],

        'or-coordinator' => [
            'surgery_workflow' => [
                'label' => 'Surgery Workflow Assistance',
                'icon'  => 'bi-scissors',
                'desc'  => 'Assist in operating room allocation, turn-around times, and case sequencing.',
            ],
            'scheduling_assistance' => [
                'label' => 'OR Scheduling Assistance',
                'icon'  => 'bi-calendar3',
                'desc'  => 'Optimize surgical calendar slots, team availability, and equipment readiness.',
            ],
        ],

        'admin' => [
            'operational_analytics' => [
                'label' => 'Operational Analytics',
                'icon'  => 'bi-graph-up-arrow',
                'desc'  => 'Analyze system-wide clinical throughput, turnaround times, and service metrics.',
            ],
            'system_insights' => [
                'label' => 'System Insights',
                'icon'  => 'bi-lightbulb',
                'desc'  => 'Summarize platform usage patterns, workload distribution, and audit summaries.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Clinical Safety Guidelines
    |--------------------------------------------------------------------------
    */
    'safety_notice' => 'IMPORTANT: MediSense AI functions strictly as an Intelligent Clinical Decision Support Assistant. Responses are generated to assist authorized personnel and do NOT constitute autonomous medical diagnoses, binding prescriptions, or independent clinical decisions. Final decisions remain with authorized medical professionals.',

];
