<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRadiologyRequestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_id'           => 'required|exists:patients,id',
            'modality'             => 'required|string|max:50',
            'body_part'            => 'required|string|max:100',
            'clinical_information' => 'nullable|string|max:2000',
            'priority'             => 'required|in:Routine,Urgent,STAT',
        ];
    }
}
