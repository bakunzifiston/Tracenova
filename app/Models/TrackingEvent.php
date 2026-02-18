<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'type',
        'session_id',
        'user_id',
        'payload',
        'url',
        'user_agent',
        'ip',
        'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const TYPE_ERROR = 'error';
    public const TYPE_PAGE_VIEW = 'page_view';
    public const TYPE_PERFORMANCE = 'performance';
    public const TYPE_BUSINESS = 'business';
    public const TYPE_CUSTOM = 'custom';

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
