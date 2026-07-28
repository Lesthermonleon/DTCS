<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurgeryRequestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_id'          => 'required|exists:patients,id',
            'procedure_name'      => 'required|string|max:200',
            'diagnosis'           => 'nullable|string|max:500',
            'urgency'             => 'required|in:Elective,Urgent,Emergency',
            'notes'               => 'nullable|string|max:1000',
            'anesthesia_type'     => 'nullable|string|max:100',
            'estimated_duration'  => 'nullable|integer|min:10|max:600',
        ];
    }
}
