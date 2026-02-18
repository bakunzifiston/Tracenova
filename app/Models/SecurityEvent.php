<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityEvent extends Model
{
    protected $table = 'security_events';

    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'event_type',
        'ip_address',
        'user_identifier',
        'reason',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const TYPE_FAILED_LOGIN = 'failed_login';
    public const TYPE_SUSPICIOUS_ACTIVITY = 'suspicious_activity';
    public const TYPE_TOKEN_ABUSE = 'token_abuse';

    public const TYPES = [
        self::TYPE_FAILED_LOGIN => 'Failed login',
        self::TYPE_SUSPICIOUS_ACTIVITY => 'Suspicious activity',
        self::TYPE_TOKEN_ABUSE => 'Token abuse',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
