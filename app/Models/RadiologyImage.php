<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Radiology image/file uploaded by a radiologic technologist.
 */
class RadiologyImage extends Model
{

    protected $fillable = [
        'radiology_request_id', 'file_path', 'file_name',
        'file_type', 'file_size', 'uploaded_by', 'uploaded_at', 'notes',
    ];

    protected $casts = ['uploaded_at' => 'datetime'];

    public function radiologyRequest(): BelongsTo
    {
        return $this->belongsTo(RadiologyRequest::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
