<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Therapeutic diet plan created by a dietitian.
 */
class DietPlan extends Model
{
    use HasStatusBadge;

    protected $fillable = [
        'diet_request_id', 'dietitian_id', 'plan_details', 'total_calories',
        'protein_grams', 'carb_grams', 'fat_grams',
        'start_date', 'end_date', 'status', 'notes',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'protein_grams' => 'decimal:2',
        'carb_grams'    => 'decimal:2',
        'fat_grams'     => 'decimal:2',
    ];

    public function dietRequest(): BelongsTo { return $this->belongsTo(DietRequest::class); }
    public function dietitian(): BelongsTo   { return $this->belongsTo(User::class, 'dietitian_id'); }
    public function mealSchedules(): HasMany { return $this->hasMany(MealSchedule::class); }
}

