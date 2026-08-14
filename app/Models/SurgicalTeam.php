<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Surgical team with a lead surgeon and multiple members.
 */
class SurgicalTeam extends Model
{

    protected $fillable = ['name', 'surgeon_id', 'notes', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function surgeon(): BelongsTo    { return $this->belongsTo(User::class, 'surgeon_id'); }
    public function members(): HasMany      { return $this->hasMany(SurgicalTeamMember::class); }
    public function schedules(): HasMany    { return $this->hasMany(SurgerySchedule::class); }
}
