<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDietPlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'plan_details'   => 'required|string|min:20',
            'total_calories' => 'nullable|integer|min:500|max:5000',
            'protein_grams'  => 'nullable|numeric|min:0',
            'carb_grams'     => 'nullable|numeric|min:0',
            'fat_grams'      => 'nullable|numeric|min:0',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after:start_date',
            'notes'          => 'nullable|string|max:1000',
        ];
    }
}
