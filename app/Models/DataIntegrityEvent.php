<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataIntegrityEvent extends Model
{
    protected $table = 'data_integrity_events';

    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'event_type',
        'reference_id',
        'description',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const TYPE_NEGATIVE_STOCK = 'negative_stock';
    public const TYPE_DUPLICATE_ORDER = 'duplicate_order';
    public const TYPE_MISSING_TRANSACTION = 'missing_transaction';

    public const TYPES = [
        self::TYPE_NEGATIVE_STOCK => 'Negative stock',
        self::TYPE_DUPLICATE_ORDER => 'Duplicate order',
        self::TYPE_MISSING_TRANSACTION => 'Missing transaction',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
