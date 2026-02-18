<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JourneyEvent extends Model
{
    protected $fillable = [
        'app_id', 'environment', 'session_id', 'user_id',
        'step_name', 'step_type', 'payload', 'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const TYPE_SCREEN = 'screen';
    public const TYPE_ACTION = 'action';
    public const TYPE_CUSTOM = 'custom';

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
