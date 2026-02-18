<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorEvent extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'message',
        'stack_trace',
        'file',
        'line',
        'severity',
        'user_info',
        'device_info',
        'context',
        'occurred_at',
        'url',
        'user_agent',
        'ip',
    ];

    protected $casts = [
        'user_info' => 'array',
        'device_info' => 'array',
        'context' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const SEVERITY_DEBUG = 'debug';
    public const SEVERITY_INFO = 'info';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_ERROR = 'error';
    public const SEVERITY_CRITICAL = 'critical';

    public const SEVERITIES = [
        self::SEVERITY_DEBUG => 'Debug',
        self::SEVERITY_INFO => 'Info',
        self::SEVERITY_WARNING => 'Warning',
        self::SEVERITY_ERROR => 'Error',
        self::SEVERITY_CRITICAL => 'Critical',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
