<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'prescription_id'        => 'nullable|exists:prescriptions,id',
            'prescription_item_ids'  => 'nullable|array|min:1',
            'prescription_item_ids.*'=> 'exists:prescription_items,id',
            'prescription_item_id'   => 'nullable|exists:prescription_items,id',

            'items'                      => 'nullable|array|min:1',
            'items.*.id'                 => 'nullable|exists:prescription_items,id',
            'items.*.quantity_dispensed' => 'nullable|integer|min:1',
            'items.*.lot_number'         => 'nullable|string|max:50',
            'items.*.expiry_date'        => 'nullable|date|after:today',
            'items.*.notes'              => 'nullable|string|max:500',

            'quantity_dispensed'   => 'nullable|integer|min:1',
            'lot_number'           => 'nullable|string|max:50',
            'expiry_date'          => 'nullable|date|after:today',
            'notes'                => 'nullable|string|max:500',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $hasRx     = !empty($this->input('prescription_id'));
            $hasSingle = !empty($this->input('prescription_item_id'));
            $hasArray  = !empty($this->input('prescription_item_ids')) && is_array($this->input('prescription_item_ids'));
            $hasItems  = !empty($this->input('items')) && is_array($this->input('items'));

            if (! $hasRx && ! $hasSingle && ! $hasArray && ! $hasItems) {
                $validator->errors()->add('prescription_id', 'Please select a valid prescription or medication items to dispense.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'prescription_item_ids.required' => 'Please select at least one prescription item to dispense.',
            'prescription_item_ids.min'      => 'Please select at least one prescription item to dispense.',
            'prescription_item_id.required'  => 'Please select a valid prescription item to dispense.',
            'prescription_item_id.exists'    => 'The selected prescription item does not exist.',
            'quantity_dispensed.min'         => 'Quantity dispensed must be at least 1.',
            'lot_number.required'            => 'Lot / Batch number is required for medication tracking.',
            'expiry_date.required'           => 'Medication expiry date is required.',
            'expiry_date.after'              => 'Expiry date must be in the future.',
            'items.*.lot_number.required'    => 'Lot / Batch number is required for each medication.',
            'items.*.expiry_date.required'   => 'Expiry date is required for each medication.',
            'items.*.expiry_date.after'      => 'Expiry date must be in the future (expired medications cannot be dispensed).',
        ];
    }
}

