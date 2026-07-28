<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabRequestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_id'      => 'required|exists:patients,id',
            'priority'        => 'required|in:Routine,Urgent,STAT',
            'specimen_type'   => 'required|string|max:50',
            'clinical_notes'  => 'nullable|string|max:1000',
            'tests'           => 'required|array|min:1',
            'tests.*'         => 'exists:lab_tests,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tests.required' => 'Please select at least one laboratory test.',
            'tests.*.exists' => 'One or more selected tests are invalid.',
        ];
    }
}
