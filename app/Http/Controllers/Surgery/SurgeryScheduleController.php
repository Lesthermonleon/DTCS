<?php

namespace App\Http\Controllers\Surgery;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurgeryScheduleRequest;
use App\Models\OperatingRoom;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\SurgicalTeam;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * SurgeryScheduleController — OR Coordinator schedules and manages surgery events.
 */
class SurgeryScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $query = SurgerySchedule::with('surgeryRequest.patient', 'operatingRoom', 'surgicalTeam.surgeon', 'scheduledBy');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('surgeryRequest', function ($sq) use ($search) {
                    $sq->where('request_no', 'like', "%{$search}%")
                       ->orWhere('procedure_name', 'like', "%{$search}%")
                       ->orWhereHas('patient', function ($pq) use ($search) {
                           $pq->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                       });
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('operating_room_id')) {
            $query->where('operating_room_id', $request->operating_room_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        $schedules = $query->latest('scheduled_at')->paginate(15)->withQueryString();
        $operatingRooms = OperatingRoom::where('is_active', true)->get();
        $statuses = ['Scheduled', 'In Progress', 'Completed', 'Postponed', 'Cancelled'];

        return view('surgery.schedules.index', compact('schedules', 'operatingRooms', 'statuses'));
    }

    public function create(Request $request): View
    {
        $this->ensureRoomsAndTeamsExist();

        $selectedRequestId = $request->query('request');
        $pendingRequests   = SurgeryRequest::whereIn('status', ['Pending', 'Scheduled'])->with('patient')->get();
        $operatingRooms    = OperatingRoom::where('is_active', true)->get();
        $surgicalTeams     = SurgicalTeam::where('is_active', true)->with('surgeon')->get();

        return view('surgery.schedules.create', compact('pendingRequests', 'operatingRooms', 'surgicalTeams', 'selectedRequestId'));
    }

    public function store(StoreSurgeryScheduleRequest $request): RedirectResponse
    {
        $schedule = SurgerySchedule::create(array_merge($request->validated(), [
            'scheduled_by' => Auth::id(),
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

        $this->ensureRoomsAndTeamsExist();

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
                         ->with('success', 'Schedule updated successfully.');
    }

    public function destroy(SurgerySchedule $surgerySchedule): RedirectResponse
    {
        abort_if($surgerySchedule->status === 'Completed', 403, 'Completed schedules cannot be deleted.');
        if ($surgerySchedule->surgeryRequest) {
            $surgerySchedule->surgeryRequest->update(['status' => 'Pending']);
        }
        $surgerySchedule->delete();

        return redirect()->route('surgery.schedules.index')
                         ->with('success', 'Schedule removed. Request returned to pending.');
    }

    public function start(SurgerySchedule $surgerySchedule): RedirectResponse
    {
        $surgerySchedule->update(['status' => 'In Progress']);
        if ($surgerySchedule->surgeryRequest) {
            $surgerySchedule->surgeryRequest->update(['status' => 'In Progress']);
        }

        return back()->with('success', 'Surgery marked as In Progress.');
    }

    public function complete(SurgerySchedule $surgerySchedule): RedirectResponse
    {
        $surgerySchedule->update(['status' => 'Completed']);
        if ($surgerySchedule->surgeryRequest) {
            $surgerySchedule->surgeryRequest->update(['status' => 'Completed']);
        }

        return back()->with('success', 'Surgery marked as completed.');
    }

    /** FullCalendar JSON events feed. */
    public function calendarEvents(): \Illuminate\Http\JsonResponse
    {
        $events = SurgerySchedule::with('surgeryRequest.patient', 'surgeryRequest.doctor', 'operatingRoom', 'surgicalTeam.surgeon')
                  ->whereNotIn('status', ['Cancelled'])
                  ->get()
                  ->map(fn($s) => [
                      'id'    => $s->id,
                      'title' => ($s->surgeryRequest->patient->last_name ?? '?') . ' — ' . ($s->surgeryRequest->procedure_name ?? 'Procedure'),
                      'start' => $s->scheduled_at->toIso8601String(),
                      'end'   => $s->scheduled_at->copy()->addMinutes($s->duration_minutes)->toIso8601String(),
                      'color' => match ($s->status) {
                          'Scheduled'   => '#3b82f6',
                          'In Progress' => '#f59e0b',
                          'Completed'   => '#10b981',
                          'Postponed'   => '#64748b',
                          default       => '#ef4444',
                      },
                      'extendedProps' => [
                          'or'           => $s->operatingRoom->name ?? 'OR',
                          'status'       => $s->status,
                          'patient_name' => ($s->surgeryRequest->patient->first_name ?? '') . ' ' . ($s->surgeryRequest->patient->last_name ?? ''),
                          'surgeon'      => $s->surgicalTeam->surgeon->name ?? 'Surgeon',
                          'request_by'   => $s->surgeryRequest->doctor->name ?? 'Doctor',
                          'procedure'    => $s->surgeryRequest->procedure_name ?? 'Procedure',
                          'duration'     => $s->duration_minutes,
                          'scheduled_at' => $s->scheduled_at->format('M d, Y · h:i A'),
                      ],
                  ]);

        return response()->json($events);
    }

    /** Calendar view page. */
    public function calendar(): View
    {
        return view('surgery.calendar');
    }

    /** Helper to ensure sample ORs and Teams exist if database lacks them. */
    private function ensureRoomsAndTeamsExist(): void
    {
        if (OperatingRoom::count() === 0) {
            $defaultRooms = [
                ['name' => 'OR-1 (General Surgery)', 'location' => '3rd Floor, Surgical Wing', 'status' => 'Available', 'is_active' => true],
                ['name' => 'OR-2 (Orthopedics)',      'location' => '3rd Floor, Surgical Wing', 'status' => 'Available', 'is_active' => true],
                ['name' => 'OR-3 (Cardiac Center)',   'location' => '4th Floor, Cardiac Center', 'status' => 'Available', 'is_active' => true],
                ['name' => 'OR-4 (Neuro & Trauma)',   'location' => 'Ground Floor, ER Wing',   'status' => 'Available', 'is_active' => true],
            ];
            foreach ($defaultRooms as $room) {
                OperatingRoom::create($room);
            }
        }

        if (SurgicalTeam::count() === 0) {
            $doctor = User::whereHas('roles', fn($q) => $q->where('slug', 'doctor'))->first() ?? User::first();
            if ($doctor) {
                SurgicalTeam::create([
                    'name'       => 'General Surgery Team Alpha',
                    'surgeon_id' => $doctor->id,
                    'notes'      => 'Primary General Surgery Team',
                    'is_active'  => true,
                ]);
            }
        }
    }
}
