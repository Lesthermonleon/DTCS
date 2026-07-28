<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Activity log for auditing user actions across all modules.
 */
class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'module', 'description',
        'loggable_type', 'loggable_id', 'ip_address', 'logged_at',
    ];

    protected $casts = ['logged_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    /**
     * Polymorphic relation to the entity being logged.
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}
