<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabResultRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'result_value' => 'required|string|max:255',
            'remarks'      => 'nullable|string|max:1000',
        ];
    }
}
