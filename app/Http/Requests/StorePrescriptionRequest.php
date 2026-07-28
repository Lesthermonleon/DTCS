<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_id'     => 'required|exists:patients,id',
            'diagnosis'      => 'nullable|string|max:500',
            'notes'          => 'nullable|string|max:1000',
            'items'          => 'required|array|min:1',
            'items.*.medication_name' => 'required|string|max:200',
            'items.*.dosage'          => 'required|string|max:50',
            'items.*.route'           => 'nullable|string|max:50',
            'items.*.frequency'       => 'required|string|max:50',
            'items.*.duration'        => 'required|string|max:50',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.instructions'    => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please add at least one medication.',
        ];
    }
}
