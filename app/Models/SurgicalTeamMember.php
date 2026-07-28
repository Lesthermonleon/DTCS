<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single member of a surgical team, with their specific role.
 */
class SurgicalTeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['surgical_team_id', 'user_id', 'role_in_team'];

    public function team(): BelongsTo { return $this->belongsTo(SurgicalTeam::class, 'surgical_team_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
