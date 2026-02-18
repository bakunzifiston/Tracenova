<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $table = 'alerts';

    protected $fillable = [
        'app_id',
        'environment',
        'alert_type',
        'title',
        'message',
        'severity',
        'payload',
        'channel',
        'occurred_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public const TYPE_REVENUE_RISK = 'revenue_risk';
    public const TYPE_ERROR_SPIKE = 'error_spike';
    public const TYPE_PERFORMANCE_DROP = 'performance_drop';

    public const TYPES = [
        self::TYPE_REVENUE_RISK => 'Revenue risk',
        self::TYPE_ERROR_SPIKE => 'Error spike',
        self::TYPE_PERFORMANCE_DROP => 'Performance drop',
    ];

    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_CRITICAL = 'critical';

    public const SEVERITIES = [
        self::SEVERITY_LOW => 'Low',
        self::SEVERITY_MEDIUM => 'Medium',
        self::SEVERITY_HIGH => 'High',
        self::SEVERITY_CRITICAL => 'Critical',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
