<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Scheduled surgery linking a surgery request, operating room, and surgical team.
 */
class SurgerySchedule extends Model
{
    use HasStatusBadge;

    protected $fillable = [
        'surgery_request_id', 'operating_room_id', 'surgical_team_id',
        'scheduled_by', 'scheduled_at', 'duration_minutes', 'status', 'notes',
    ];

    protected $casts = ['scheduled_at' => 'datetime'];

    public function surgeryRequest(): BelongsTo  { return $this->belongsTo(SurgeryRequest::class); }
    public function operatingRoom(): BelongsTo   { return $this->belongsTo(OperatingRoom::class); }
    public function surgicalTeam(): BelongsTo    { return $this->belongsTo(SurgicalTeam::class); }
    public function scheduledBy(): BelongsTo     { return $this->belongsTo(User::class, 'scheduled_by'); }

    
}

