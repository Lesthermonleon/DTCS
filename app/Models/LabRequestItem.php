<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Line item within a lab request; one per test ordered.
 */
class LabRequestItem extends Model
{
    use HasFactory;

    protected $fillable = ['lab_request_id', 'lab_test_id', 'status'];

    public function labRequest(): BelongsTo
    {
        return $this->belongsTo(LabRequest::class);
    }

    public function labTest(): BelongsTo
    {
        return $this->belongsTo(LabTest::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(LabResult::class);
    }
}
