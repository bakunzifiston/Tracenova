<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class App extends Model
{
    protected $table = 'apps';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'platform',
        'platform_id',
        'user_id',
        'is_tracking_enabled',
        'settings',
    ];

    protected $casts = [
        'is_tracking_enabled' => 'boolean',
        'settings' => 'array',
    ];

    public const PLATFORMS = [
        'web' => 'Web',
        'react_native' => 'React Native',
        'wordpress' => 'WordPress',
        'php' => 'PHP',
        'ios' => 'iOS',
        'android' => 'Android',
        'flutter' => 'Flutter',
        'other' => 'Other',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    /** Scope to apps owned by the given user (for data isolation). */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class, 'app_id');
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class, 'app_id');
    }

    public function trackingSessions(): HasMany
    {
        return $this->hasMany(TrackingSession::class, 'app_id');
    }

    public function navigationEvents(): HasMany
    {
        return $this->hasMany(NavigationEvent::class, 'app_id');
    }

    public function screenViews(): HasMany
    {
        return $this->hasMany(ScreenView::class, 'app_id');
    }

    public function userActions(): HasMany
    {
        return $this->hasMany(UserAction::class, 'app_id');
    }

    public function errorEvents(): HasMany
    {
        return $this->hasMany(ErrorEvent::class, 'app_id');
    }

    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(PerformanceMetric::class, 'app_id');
    }

    public function funnels(): HasMany
    {
        return $this->hasMany(Funnel::class, 'app_id');
    }

    public function journeyEvents(): HasMany
    {
        return $this->hasMany(JourneyEvent::class, 'app_id');
    }

    public function businessEvents(): HasMany
    {
        return $this->hasMany(BusinessEvent::class, 'app_id');
    }

    public function financialImpacts(): HasMany
    {
        return $this->hasMany(FinancialImpact::class, 'app_id');
    }

    public function networkMonitoringEvents(): HasMany
    {
        return $this->hasMany(NetworkMonitoringEvent::class, 'app_id');
    }

    public function thirdPartyApiEvents(): HasMany
    {
        return $this->hasMany(ThirdPartyApiEvent::class, 'app_id');
    }

    public function featureUsageEvents(): HasMany
    {
        return $this->hasMany(FeatureUsageEvent::class, 'app_id');
    }

    public function moduleHealthScores(): HasMany
    {
        return $this->hasMany(ModuleHealthScore::class, 'app_id');
    }

    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class, 'app_id');
    }

    public function dataIntegrityEvents(): HasMany
    {
        return $this->hasMany(DataIntegrityEvent::class, 'app_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'app_id');
    }

    public function scopeTrackingEnabled($query)
    {
        return $query->where('is_tracking_enabled', true);
    }
}
