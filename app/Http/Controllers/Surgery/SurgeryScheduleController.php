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
        abort_if(! Auth::user()?->hasRole('or-coordinator'), 403, 'Only OR coordinators can schedule surgical procedures.');

        $this->ensureRoomsAndTeamsExist();

        $selectedRequestId = $request->query('request');
        $pendingRequests   = SurgeryRequest::whereIn('status', ['Pending', 'Scheduled'])->with('patient')->get();
        $operatingRooms    = OperatingRoom::where('is_active', true)->get();
        $surgicalTeams     = SurgicalTeam::where('is_active', true)->with('surgeon')->get();

        return view('surgery.schedules.create', compact('pendingRequests', 'operatingRooms', 'surgicalTeams', 'selectedRequestId'));
    }

    public function store(StoreSurgeryScheduleRequest $request): RedirectResponse
    {
        abort_if(! Auth::user()?->hasRole('or-coordinator'), 403, 'Only OR coordinators can schedule surgical procedures.');

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
        abort_if(! Auth::user()?->hasRole('or-coordinator'), 403, 'Only OR coordinators can edit surgical schedules.');
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

    /** Print-friendly view for surgery schedule. */
    public function print(SurgerySchedule $surgerySchedule): View
    {
        $surgerySchedule->load('surgeryRequest.patient', 'surgeryRequest.doctor', 'coordinator');

        return view('surgery.schedules.print', compact('surgerySchedule'));
    }

    /** FullCalendar JSON events feed. */
    public function calendarEvents(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = SurgerySchedule::with('surgeryRequest.patient', 'surgeryRequest.doctor', 'operatingRoom', 'surgicalTeam.surgeon')
                  ->whereNotIn('status', ['Cancelled']);

        if ($request->filled('operating_room_id')) {
            $query->where('operating_room_id', $request->operating_room_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->get()->map(fn($s) => [
            'id'    => $s->id,
            'title' => ($s->surgeryRequest->patient->last_name ?? '?') . ' — ' . ($s->surgeryRequest->procedure_name ?? 'Procedure'),
            'start' => $s->scheduled_at->toIso8601String(),
            'end'   => $s->scheduled_at->copy()->addMinutes($s->duration_minutes ?? 60)->toIso8601String(),
            'color' => match ($s->status) {
                'Scheduled'   => '#0d6efd',
                'In Progress' => '#fd7e14',
                'Completed'   => '#198754',
                'Postponed'   => '#6c757d',
                default       => '#dc3545',
            },
            'extendedProps' => [
                'or'             => $s->operatingRoom->name ?? 'OR',
                'or_id'          => $s->operating_room_id,
                'status'         => $s->status,
                'patient_name'   => ($s->surgeryRequest->patient->first_name ?? '') . ' ' . ($s->surgeryRequest->patient->last_name ?? ''),
                'patient_no'     => $s->surgeryRequest->patient->patient_no ?? '',
                'surgeon'        => $s->surgicalTeam->surgeon->name ?? 'Surgeon',
                'request_by'     => $s->surgeryRequest->doctor->name ?? 'Doctor',
                'request_no'     => $s->surgeryRequest->request_no ?? '',
                'procedure'      => $s->surgeryRequest->procedure_name ?? 'Procedure',
                'urgency'        => $s->surgeryRequest->urgency ?? 'Elective',
                'duration'       => $s->duration_minutes ?? 60,
                'scheduled_at'   => $s->scheduled_at->format('M d, Y · h:i A'),
                'scheduled_date' => $s->scheduled_at->format('Y-m-d'),
                'start_time'     => $s->scheduled_at->format('g:i A'),
                'end_time'       => $s->scheduled_at->copy()->addMinutes($s->duration_minutes ?? 60)->format('g:i A'),
            ],
        ]);

        return response()->json($events);
    }

    /** Calendar view page. */
    public function calendar(): View
    {
        $operatingRooms = OperatingRoom::where('is_active', true)->get();
        $upcomingSchedules = SurgerySchedule::with('surgeryRequest.patient', 'operatingRoom', 'surgicalTeam.surgeon')
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->orderBy('scheduled_at', 'asc')
            ->take(30)
            ->get();

        return view('surgery.calendar', compact('operatingRooms', 'upcomingSchedules'));
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
