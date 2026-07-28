<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Individual medication line item within a prescription.
 */
class PrescriptionItem extends Model
{
    use HasStatusBadge;
    use HasFactory;

    protected $fillable = [
        'prescription_id', 'medication_name', 'dosage', 'route',
        'frequency', 'duration', 'quantity', 'instructions', 'status',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function dispensingRecords(): HasMany
    {
        return $this->hasMany(DispensingRecord::class);
    }
}

