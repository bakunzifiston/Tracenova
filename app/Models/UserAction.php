<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAction extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'action_type',
        'action_name',
        'target',
        'payload',
        'occurred_at',
        'url',
        'user_agent',
        'ip',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const TYPE_BUTTON_CLICK = 'button_click';
    public const TYPE_DASHBOARD_ACCESS = 'dashboard_access';
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_FORM_SUBMISSION = 'form_submission';
    public const TYPE_CUSTOM = 'custom';

    public const TYPES = [
        self::TYPE_BUTTON_CLICK => 'Button click',
        self::TYPE_DASHBOARD_ACCESS => 'Dashboard access',
        self::TYPE_PAYMENT => 'Payment',
        self::TYPE_FORM_SUBMISSION => 'Form submission',
        self::TYPE_CUSTOM => 'Custom',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
