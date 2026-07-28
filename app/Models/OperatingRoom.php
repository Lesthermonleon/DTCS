<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Operating room available for surgical procedures.
 */
class OperatingRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'status', 'equipment', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function surgerySchedules(): HasMany
    {
        return $this->hasMany(SurgerySchedule::class);
    }
}
