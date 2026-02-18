<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThirdPartyApiEvent extends Model
{
    protected $table = 'third_party_api_events';

    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'provider_type',
        'provider_name',
        'operation',
        'success',
        'response_time_ms',
        'status_code',
        'error_message',
        'request_id',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const TYPE_PAYMENT = 'payment';
    public const TYPE_SMS = 'sms';
    public const TYPE_EMAIL = 'email';

    public const TYPES = [
        self::TYPE_PAYMENT => 'Payment',
        self::TYPE_SMS => 'SMS',
        self::TYPE_EMAIL => 'Email',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
