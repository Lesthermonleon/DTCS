<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurgeryScheduleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'surgery_request_id'  => 'required|exists:surgery_requests,id',
            'operating_room_id'   => 'required|exists:operating_rooms,id',
            'surgical_team_id'    => 'required|exists:surgical_teams,id',
            'scheduled_at'        => 'required|date|after:now',
            'duration_minutes'    => 'required|integer|min:15|max:600',
            'notes'               => 'nullable|string|max:1000',
        ];
    }
}
