<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispensingRecordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'prescription_item_id' => 'required|exists:prescription_items,id',
            'quantity_dispensed'   => 'required|integer|min:1',
            'lot_number'           => 'nullable|string|max:50',
            'expiry_date'          => 'nullable|date|after:today',
            'notes'                => 'nullable|string|max:500',
        ];
    }
}
