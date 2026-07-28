<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Individual laboratory test (e.g. CBC, Blood Glucose).
 */
class LabTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_test_category_id',
        'name',
        'code',
        'normal_range',
        'unit',
        'method',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(LabTestCategory::class, 'lab_test_category_id');
    }

    public function requestItems(): HasMany
    {
        return $this->hasMany(LabRequestItem::class);
    }
}
