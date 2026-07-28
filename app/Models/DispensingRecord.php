<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Record of a dispensing event by a pharmacist.
 */
class DispensingRecord extends Model
{
    use HasStatusBadge;
    use HasFactory;

    protected $fillable = [
        'prescription_item_id', 'pharmacist_id', 'quantity_dispensed',
        'lot_number', 'expiry_date', 'notes', 'dispensed_at',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
        'expiry_date'  => 'date',
    ];

    public function prescriptionItem(): BelongsTo
    {
        return $this->belongsTo(PrescriptionItem::class);
    }

    public function pharmacist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }
}

