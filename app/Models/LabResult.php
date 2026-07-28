<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lab result encoded by a medical technologist.
 */
class LabResult extends Model
{
    use HasStatusBadge;
    use HasFactory;

    protected $fillable = [
        'lab_request_item_id',
        'technologist_id',
        'result_value',
        'remarks',
        'status',
        'validated_by',
        'released_by',
        'validated_at',
        'released_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'released_at'  => 'datetime',
    ];

    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(LabRequestItem::class, 'lab_request_item_id');
    }

    public function technologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technologist_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    
}

