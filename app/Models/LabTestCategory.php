<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Lab Test Category model (e.g. Hematology, Clinical Chemistry).
 */
class LabTestCategory extends Model
{

    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function labTests(): HasMany
    {
        return $this->hasMany(LabTest::class);
    }
}
