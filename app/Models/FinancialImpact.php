<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialImpact extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'impact_type',
        'amount',
        'currency',
        'reference_id',
        'description',
        'metadata',
        'occurred_at',
        'session_id',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const TYPE_FAILED_PAYMENT = 'failed_payment';
    public const TYPE_SYSTEM_ERROR = 'system_error';
    public const TYPE_DOWNTIME = 'downtime';
    public const TYPE_CUSTOM = 'custom';

    public const TYPES = [
        self::TYPE_FAILED_PAYMENT => 'Failed payment',
        self::TYPE_SYSTEM_ERROR => 'System error',
        self::TYPE_DOWNTIME => 'Downtime',
        self::TYPE_CUSTOM => 'Custom',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
