<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingSession extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'foreground_seconds',
        'background_seconds',
        'last_activity_at',
        'metadata',
        'user_agent',
        'ip',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'metadata' => 'array',
    ];

    /** Consider session active if no end time and activity in last 30 minutes */
    public const ACTIVE_THRESHOLD_MINUTES = 30;

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }

    public function isActive(): bool
    {
        if ($this->ended_at !== null) {
            return false;
        }
        if (!$this->last_activity_at) {
            return true;
        }
        return $this->last_activity_at->gte(now()->subMinutes(self::ACTIVE_THRESHOLD_MINUTES));
    }

    public function scopeActive($query)
    {
        return $query->whereNull('ended_at')
            ->where(function ($q) {
                $q->whereNull('last_activity_at')
                    ->orWhere('last_activity_at', '>=', now()->subMinutes(self::ACTIVE_THRESHOLD_MINUTES));
            });
    }

    public function scopeEnded($query)
    {
        return $query->whereNotNull('ended_at');
    }
}
