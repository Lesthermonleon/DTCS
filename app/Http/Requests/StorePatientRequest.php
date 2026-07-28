<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_no'              => 'sometimes|unique:patients,patient_no',
            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'middle_name'             => 'nullable|string|max:100',
            'date_of_birth'           => 'required|date|before:today',
            'gender'                  => 'required|in:Male,Female,Other',
            'blood_type'              => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'address'                 => 'nullable|string|max:500',
            'phone'                   => 'nullable|string|max:20',
            'email'                   => 'nullable|email|max:255',
            'emergency_contact_name'  => 'nullable|string|max:200',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'patient_type'            => 'required|in:Inpatient,Outpatient',
            'ward'                    => 'nullable|string|max:100',
            'bed_number'              => 'nullable|string|max:20',
        ];
    }
}
