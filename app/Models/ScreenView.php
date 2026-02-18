<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreenView extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'screen_name',
        'previous_screen',
        'occurred_at',
        'load_time_ms',
        'metadata',
        'user_agent',
        'ip',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
