<?php

namespace App\Http\Controllers\Surgery;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurgeryScheduleRequest;
use App\Models\OperatingRoom;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\SurgicalTeam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SurgeryScheduleController — OR Coordinator schedules and manages surgery events.
 */
class SurgeryScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = SurgerySchedule::with('surgeryRequest.patient', 'operatingRoom', 'surgicalTeam', 'scheduledBy')
                     ->latest('scheduled_at')->paginate(15)->withQueryString();

        return view('surgery.schedules.index', compact('schedules'));
    }

    public function create(): View
    {
        $pendingRequests = SurgeryRequest::whereIn('status', ['Pending', 'Scheduled'])->with('patient')->get();
        $operatingRooms  = OperatingRoom::where('is_active', true)->get();
        $surgicalTeams   = SurgicalTeam::where('is_active', true)->with('surgeon')->get();

        return view('surgery.schedules.create', compact('pendingRequests', 'operatingRooms', 'surgicalTeams'));
    }

    public function store(StoreSurgeryScheduleRequest $request): RedirectResponse
    {
        $schedule = SurgerySchedule::create(array_merge($request->validated(), [
            'scheduled_by' => auth()->id(),
            'status'       => 'Scheduled',
        ]));

        // Update the surgery request status
        SurgeryRequest::find($request->surgery_request_id)->update(['status' => 'Scheduled']);

        return redirect()->route('surgery.schedules.index')
                         ->with('success', 'Surgery scheduled successfully.');
    }

    public function show(SurgerySchedule $surgerySchedule): View
    {
        $surgerySchedule->load('surgeryRequest.patient', 'surgeryRequest.doctor', 'operatingRoom', 'surgicalTeam.surgeon', 'surgicalTeam.members.user', 'scheduledBy');

        return view('surgery.schedules.show', compact('surgerySchedule'));
    }

    public function edit(SurgerySchedule $surgerySchedule): View
    {
        abort_if($surgerySchedule->status === 'Completed', 403, 'Completed schedules cannot be edited.');

        $pendingRequests = SurgeryRequest::whereIn('status', ['Pending', 'Scheduled'])->with('patient')->get();
        $operatingRooms  = OperatingRoom::where('is_active', true)->get();
        $surgicalTeams   = SurgicalTeam::where('is_active', true)->with('surgeon')->get();

        return view('surgery.schedules.edit', compact('surgerySchedule', 'pendingRequests', 'operatingRooms', 'surgicalTeams'));
    }

    public function update(StoreSurgeryScheduleRequest $request, SurgerySchedule $surgerySchedule): RedirectResponse
    {
        abort_if($surgerySchedule->status === 'Completed', 403, 'Completed schedules cannot be edited.');
        $surgerySchedule->update($request->validated());

        return redirect()->route('surgery.schedules.show', $surgerySchedule)
                         ->with('success', 'Schedule updated.');
    }

    public function destroy(SurgerySchedule $surgerySchedule): RedirectResponse
    {
        abort_if($surgerySchedule->status === 'Completed', 403, 'Completed schedules cannot be deleted.');
        $surgerySchedule->surgeryRequest->update(['status' => 'Pending']);
        $surgerySchedule->delete();

        return redirect()->route('surgery.schedules.index')
                         ->with('success', 'Schedule removed. Request returned to pending.');
    }

    public function complete(SurgerySchedule $surgerySchedule): RedirectResponse
    {
        $surgerySchedule->update(['status' => 'Completed']);
        $surgerySchedule->surgeryRequest->update(['status' => 'Completed']);

        return back()->with('success', 'Surgery marked as completed.');
    }

    /** FullCalendar JSON events feed. */
    public function calendarEvents(): \Illuminate\Http\JsonResponse
    {
        $events = SurgerySchedule::with('surgeryRequest.patient', 'operatingRoom')
                  ->whereNotIn('status', ['Cancelled'])
                  ->get()
                  ->map(fn($s) => [
                      'id'    => $s->id,
                      'title' => ($s->surgeryRequest->patient->last_name ?? '?') . ' — ' . $s->surgeryRequest->procedure_name,
                      'start' => $s->scheduled_at->toIso8601String(),
                      'end'   => $s->scheduled_at->addMinutes($s->duration_minutes)->toIso8601String(),
                      'color' => match ($s->status) {
                          'Scheduled'   => '#0d6efd',
                          'In Progress' => '#ffc107',
                          'Completed'   => '#198754',
                          'Postponed'   => '#6c757d',
                          default       => '#dc3545',
                      },
                      'extendedProps' => [
                          'or'     => $s->operatingRoom->name,
                          'status' => $s->status,
                      ],
                  ]);

        return response()->json($events);
    }

    /** Calendar view page. */
    public function calendar(): View
    {
        return view('surgery.calendar');
    }
}
