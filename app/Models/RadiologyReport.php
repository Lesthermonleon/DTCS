<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Diagnostic report authored by a radiologist.
 */
class RadiologyReport extends Model
{
    use HasStatusBadge;

    protected $fillable = [
        'radiology_request_id', 'radiologist_id', 'findings', 'impression',
        'recommendations', 'status', 'approved_by', 'released_by',
        'approved_at', 'released_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function radiologyRequest(): BelongsTo  { return $this->belongsTo(RadiologyRequest::class); }
    public function radiologist(): BelongsTo       { return $this->belongsTo(User::class, 'radiologist_id'); }
    public function approvedBy(): BelongsTo        { return $this->belongsTo(User::class, 'approved_by'); }
    public function releasedBy(): BelongsTo        { return $this->belongsTo(User::class, 'released_by'); }

    
}

