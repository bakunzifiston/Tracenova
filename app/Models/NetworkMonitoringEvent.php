<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetworkMonitoringEvent extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'event_type',
        'occurred_at',
        'duration_seconds',
        'retry_count',
        'success',
        'network_strength',
        'payload',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'success' => 'boolean',
        'payload' => 'array',
    ];

    public const TYPE_OFFLINE_START = 'offline_start';
    public const TYPE_OFFLINE_END = 'offline_end';
    public const TYPE_SYNC_RETRY = 'sync_retry';
    public const TYPE_NETWORK_STRENGTH = 'network_strength';

    public const TYPES = [
        self::TYPE_OFFLINE_START => 'Offline start',
        self::TYPE_OFFLINE_END => 'Offline end',
        self::TYPE_SYNC_RETRY => 'Sync retry',
        self::TYPE_NETWORK_STRENGTH => 'Network strength',
    ];

    public const STRENGTH_WEAK = 'weak';
    public const STRENGTH_MODERATE = 'moderate';
    public const STRENGTH_STRONG = 'strong';

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
