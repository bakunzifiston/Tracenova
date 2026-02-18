<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureUsageEvent extends Model
{
    protected $table = 'feature_usage_events';

    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'feature_name',
        'feature_category',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
