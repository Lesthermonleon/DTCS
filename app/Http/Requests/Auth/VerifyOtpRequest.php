<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for OTP verification submission.
 *
 * Validates that the submitted OTP is a 6-digit numeric string.
 * Leading zeros are valid (e.g., '042781').
 */
class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'Please enter the verification code.',
            'otp.digits'   => 'The verification code must be exactly 6 digits.',
        ];
    }
}
