<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessEvent extends Model
{
    protected $fillable = [
        'app_id',
        'environment',
        'session_id',
        'user_id',
        'event_type',
        'reference_id',
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

    public const TYPE_ORDER_CREATED = 'order_created';
    public const TYPE_PAYMENT_COMPLETED = 'payment_completed';
    public const TYPE_INVENTORY_UPDATE = 'inventory_update';
    public const TYPE_PRODUCT_REQUEST = 'product_request';
    public const TYPE_CUSTOM = 'custom';

    public const TYPES = [
        self::TYPE_ORDER_CREATED => 'Order created',
        self::TYPE_PAYMENT_COMPLETED => 'Payment completed',
        self::TYPE_INVENTORY_UPDATE => 'Inventory update',
        self::TYPE_PRODUCT_REQUEST => 'Product request',
        self::TYPE_CUSTOM => 'Custom',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
