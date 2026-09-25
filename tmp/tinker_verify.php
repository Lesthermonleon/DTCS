<?php

App\Models\Patient::where('patient_no', 'like', '%0001%')
    ->orWhere('patient_no', 'like', 'P-2026-0001')
    ->take(3)->get()->each(function($p) {
        echo "=== {$p->patient_no} | {$p->full_name} ===\n";
        echo "Lab Requests    : " . $p->labRequests()->count() . "\n";
        echo "Radiology       : " . $p->radiologyRequests()->count() . "\n";
        echo "Prescriptions   : " . $p->prescriptions()->count() . "\n";
        echo "Surgery         : " . $p->surgeryRequests()->count() . "\n";
        echo "Diet            : " . $p->dietRequests()->count() . "\n\n";
    });
