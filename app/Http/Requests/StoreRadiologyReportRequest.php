<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRadiologyReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'radiology_request_id' => 'required|exists:radiology_requests,id',
            'findings'             => 'required|string|min:10',
            'impression'           => 'required|string|min:5',
            'recommendations'      => 'nullable|string|max:2000',
        ];
    }
}
