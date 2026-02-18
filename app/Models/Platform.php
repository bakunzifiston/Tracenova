<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Platform extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'icon',
        'description',
        'default_features',
        'sdk_config',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'default_features' => 'array',
        'sdk_config' => 'array',
        'is_active' => 'boolean',
    ];

    public const CATEGORY_BROWSER = 'Browser';
    public const CATEGORY_MOBILE = 'Mobile';
    public const CATEGORY_SERVER = 'Server';
    public const CATEGORY_DESKTOP = 'Desktop';
    public const CATEGORY_SERVERLESS = 'Serverless';
    public const CATEGORY_GAMING = 'Gaming';

    public const CATEGORIES = [
        self::CATEGORY_BROWSER => 'Browser',
        self::CATEGORY_MOBILE => 'Mobile',
        self::CATEGORY_SERVER => 'Server',
        self::CATEGORY_DESKTOP => 'Desktop',
        self::CATEGORY_SERVERLESS => 'Serverless',
        self::CATEGORY_GAMING => 'Gaming',
    ];

    public function apps(): HasMany
    {
        return $this->hasMany(App::class, 'platform_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
