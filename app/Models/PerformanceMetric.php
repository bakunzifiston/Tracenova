<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'metric_type',
        'name',
        'value',
        'value_unit',
        'is_slow',
        'threshold',
        'metadata',
        'occurred_at',
        'url',
        'user_agent',
        'ip',
        'country_code',
        'country',
        'region',
        'city',
    ];

    protected $casts = [
        'value' => 'integer',
        'is_slow' => 'boolean',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const TYPE_SCREEN_LOAD = 'screen_load';
    public const TYPE_API_RESPONSE = 'api_response';
    public const TYPE_SESSION_DURATION = 'session_duration';
    public const TYPE_CUSTOM = 'custom';

    public const TYPES = [
        self::TYPE_SCREEN_LOAD => 'Screen load time',
        self::TYPE_API_RESPONSE => 'API response time',
        self::TYPE_SESSION_DURATION => 'Session duration',
        self::TYPE_CUSTOM => 'Custom',
    ];

    public const UNIT_MS = 'ms';
    public const UNIT_SECONDS = 's';

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }

    public function scopeSlow($query)
    {
        return $query->where('is_slow', true);
    }

    /** Value formatted for display (e.g. "250 ms" or "1.5 s") */
    public function getFormattedValueAttribute(): string
    {
        return $this->value_unit === self::UNIT_SECONDS
            ? $this->value . ' s'
            : $this->value . ' ms';
    }
}
