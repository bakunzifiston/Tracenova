<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleHealthScore extends Model
{
    protected $table = 'module_health_scores';

    protected $fillable = [
        'app_id',
        'environment',
        'module_id',
        'module_name',
        'score',
        'period_type',
        'period_start',
        'period_end',
        'errors_count',
        'errors_score',
        'speed_score',
        'drop_off_score',
        'metadata',
        'recorded_at',
    ];

    protected $casts = [
        'score' => 'float',
        'errors_score' => 'float',
        'speed_score' => 'float',
        'drop_off_score' => 'float',
        'metadata' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
        'recorded_at' => 'datetime',
    ];

    public const PERIOD_DAILY = 'daily';
    public const PERIOD_WEEKLY = 'weekly';

    public const PERIODS = [
        self::PERIOD_DAILY => 'Daily',
        self::PERIOD_WEEKLY => 'Weekly',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }

    /** Score 0–100: higher is better. Return a label/color for display. */
    public function getScoreLabelAttribute(): string
    {
        if ($this->score >= 80) return 'Healthy';
        if ($this->score >= 60) return 'Fair';
        if ($this->score >= 40) return 'Degraded';
        return 'Critical';
    }
}
