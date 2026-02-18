<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FunnelStepEvent extends Model
{
    protected $fillable = [
        'funnel_id', 'environment', 'session_id', 'user_id',
        'step_key', 'step_index', 'occurred_at', 'metadata',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class, 'funnel_id');
    }
}
