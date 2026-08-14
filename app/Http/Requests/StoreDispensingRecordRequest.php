<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispensingRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->hasAnyRole(['admin', 'pharmacist']);
    }

    public function rules(): array
    {
        return [
            'prescription_item_id' => 'required|exists:prescription_items,id',
            'quantity_dispensed'   => 'required|integer|min:1',
            'lot_number'           => 'required|string|max:50',
            'expiry_date'          => 'required|date|after:today',
            'notes'                => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'prescription_item_id.required' => 'Please select a valid prescription item to dispense.',
            'prescription_item_id.exists'   => 'The selected prescription item does not exist.',
            'quantity_dispensed.required'   => 'Quantity dispensed is required.',
            'quantity_dispensed.min'        => 'Quantity dispensed must be at least 1.',
            'lot_number.required'           => 'Lot / Batch number is required for medication tracking.',
            'expiry_date.required'          => 'Medication expiry date is required.',
            'expiry_date.after'             => 'Expiry date must be in the future.',
        ];
    }
}

