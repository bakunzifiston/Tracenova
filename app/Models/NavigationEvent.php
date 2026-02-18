<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationEvent extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'from_screen',
        'to_screen',
        'navigation_type',
        'occurred_at',
        'metadata',
        'user_agent',
        'ip',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    public const TYPE_PUSH = 'push';
    public const TYPE_REPLACE = 'replace';
    public const TYPE_BACK = 'back';

    public const TYPES = [
        self::TYPE_PUSH => 'Push',
        self::TYPE_REPLACE => 'Replace',
        self::TYPE_BACK => 'Back',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
