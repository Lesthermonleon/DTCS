<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Individual meal within a diet plan schedule.
 */
class MealSchedule extends Model
{

    protected $fillable = [
        'diet_plan_id', 'meal_type', 'meal_date', 'menu', 'calories', 'is_served', 'notes',
    ];

    protected $casts = [
        'meal_date' => 'date',
        'is_served' => 'boolean',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
