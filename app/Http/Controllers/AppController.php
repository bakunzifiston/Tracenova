<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\ApiKey;
use App\Models\Platform;
use App\Services\ApiKeyService;
use App\Services\ReportFilterService;
use App\Services\SdkConfigService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppController extends Controller
{
    public function __construct(
        protected ApiKeyService $apiKeyService,
        protected SdkConfigService $sdkConfigService
    ) {}

    /**
     * List all registered apps.
     */
    public function index(Request $request): View
    {
        $apps = App::withCount('apiKeys', 'trackingEvents')
            ->with('platform')
            ->latest()
            ->paginate(10);

        return view('apps.index', compact('apps'));
    }

    /**
     * Show create form.
     */
    public function create(Request $request): View
    {
        $selectedCategory = $request->query('category');
        $platforms = Platform::active()->orderBy('sort_order')->get()->groupBy('category');
        return view('apps.create', compact('platforms', 'selectedCategory'));
    }

    /**
     * Store a new app.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:apps,slug|regex:/^[a-z0-9_-]+$/',
            'description' => 'nullable|string|max:1000',
            'platform_id' => 'required|integer|exists:platforms,id',
            'platform' => 'nullable|string|max:50', // legacy fallback
            'is_tracking_enabled' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_tracking_enabled'] = $request->boolean('is_tracking_enabled', true);
        // Set legacy platform from platform_id if not provided
        if (empty($validated['platform'])) {
            $platform = Platform::find($validated['platform_id']);
            $validated['platform'] = $platform?->slug ?? 'other';
        }

        $app = App::create($validated);

        return redirect()->route('apps.show', $app)
            ->with('success', 'App created successfully.');
    }

    /**
     * Show single app and its API keys. Supports environment and date range filters.
     */
    public function show(Request $request, App $app): View
    {
        $filterEnv = ReportFilterService::environmentFromRequest($request);
        [$filterDateFrom, $filterDateTo] = ReportFilterService::dateRangeFromRequest($request);
        $filterDatePreset = $request->query('date_preset', 'last_30_days');
        $filterDateFromInput = $request->query('date_from');
        $filterDateToInput = $request->query('date_to');

        $sessionQ = fn () => $app->trackingSessions()
            ->when($filterEnv, fn ($q) => $q->where('environment', $filterEnv))
            ->when($filterDateFrom && $filterDateTo, fn ($q) => $q->whereBetween('started_at', [$filterDateFrom, $filterDateTo]));
        $occurredQ = fn ($rel) => $rel->when($filterEnv, fn ($q) => $q->where('environment', $filterEnv))
            ->when($filterDateFrom && $filterDateTo, fn ($q) => $q->whereBetween('occurred_at', [$filterDateFrom, $filterDateTo]));
        $recordedQ = fn ($rel) => $rel->when($filterEnv, fn ($q) => $q->where('environment', $filterEnv))
            ->when($filterDateFrom && $filterDateTo, fn ($q) => $q->whereBetween('recorded_at', [$filterDateFrom, $filterDateTo]));

        $app->load('apiKeys');
        $app->loadCount('trackingEvents');
        $recentEvents = $occurredQ($app->trackingEvents())->latest()->limit(10)->get();

        $activeSessions = (clone $sessionQ())->active()->orderByDesc('last_activity_at')->limit(50)->get();
        $recentSessions = (clone $sessionQ())->ended()->orderByDesc('ended_at')->limit(20)->get();
        $sessionStats = [
            'total' => $sessionQ()->count(),
            'active_count' => (clone $sessionQ())->active()->count(),
            'unique_users' => (int) (clone $sessionQ())->whereNotNull('user_id')->selectRaw('count(distinct user_id) as cnt')->value('cnt'),
            'avg_duration_seconds' => (int) (clone $sessionQ())->ended()->avg('duration_seconds'),
            'total_foreground_seconds' => (int) $sessionQ()->sum('foreground_seconds'),
            'total_background_seconds' => (int) $sessionQ()->sum('background_seconds'),
        ];

        $recentNavigations = $occurredQ($app->navigationEvents())->latest('occurred_at')->limit(15)->get();
        $recentScreenViews = $occurredQ($app->screenViews())->latest('occurred_at')->limit(15)->get();
        $navigationStats = [
            'total' => $occurredQ($app->navigationEvents())->count(),
            'by_type' => $occurredQ($app->navigationEvents())->selectRaw('navigation_type, count(*) as count')->groupBy('navigation_type')->pluck('count', 'navigation_type'),
        ];
        $screenViewStats = [
            'total' => $occurredQ($app->screenViews())->count(),
            'avg_load_time_ms' => (int) $occurredQ($app->screenViews())->avg('load_time_ms'),
            'top_screens' => $occurredQ($app->screenViews())->selectRaw('screen_name, count(*) as views')->groupBy('screen_name')->orderByDesc('views')->limit(10)->get(),
        ];

        $recentUserActions = $occurredQ($app->userActions())->latest('occurred_at')->limit(15)->get();
        $recentErrors = $occurredQ($app->errorEvents())->latest('occurred_at')->limit(15)->get();
        $userActionStats = [
            'total' => $occurredQ($app->userActions())->count(),
            'by_type' => $occurredQ($app->userActions())->selectRaw('action_type, count(*) as count')->groupBy('action_type')->pluck('count', 'action_type'),
        ];
        $errorStats = [
            'total' => $occurredQ($app->errorEvents())->count(),
            'by_severity' => $occurredQ($app->errorEvents())->selectRaw('severity, count(*) as count')->groupBy('severity')->pluck('count', 'severity'),
        ];

        $recentPerformanceMetrics = $occurredQ($app->performanceMetrics())->latest('occurred_at')->limit(20)->get();
        $perfQ = $occurredQ($app->performanceMetrics());
        $performanceStats = [
            'total' => (clone $perfQ)->count(),
            'slow_count' => (clone $perfQ)->slow()->count(),
            'by_type' => (clone $perfQ)->selectRaw('metric_type, count(*) as count, avg(value) as avg_value')->groupBy('metric_type')->get()->keyBy('metric_type'),
        ];
        $geoPerformance = [
            'by_country' => (clone $perfQ)->whereNotNull('country')->selectRaw('country, country_code, count(*) as count, avg(value) as avg_value')->groupBy('country', 'country_code')->orderByDesc('count')->limit(15)->get(),
            'by_region' => (clone $perfQ)->whereNotNull('region')->selectRaw('country, region, count(*) as count, avg(value) as avg_value')->groupBy('country', 'region')->orderByDesc('count')->limit(15)->get(),
            'by_city' => (clone $perfQ)->whereNotNull('city')->selectRaw('country, city, count(*) as count, avg(value) as avg_value')->groupBy('country', 'city')->orderByDesc('count')->limit(15)->get(),
        ];

        $journeySessions = $activeSessions->pluck('session_id')->merge($recentSessions->pluck('session_id'))->unique()->filter()->take(20)->values();

        $recentBusinessEvents = $occurredQ($app->businessEvents())->latest('occurred_at')->limit(15)->get();
        $businessEventStats = [
            'total' => $occurredQ($app->businessEvents())->count(),
            'by_type' => $occurredQ($app->businessEvents())->selectRaw('event_type, count(*) as count')->groupBy('event_type')->pluck('count', 'event_type'),
        ];

        $recentFinancialImpacts = $occurredQ($app->financialImpacts())->latest('occurred_at')->limit(15)->get();
        $financialImpactStats = [
            'total_amount' => (float) $occurredQ($app->financialImpacts())->sum('amount'),
            'by_type' => $occurredQ($app->financialImpacts())->selectRaw('impact_type, count(*) as count, sum(amount) as total')->groupBy('impact_type')->get()->keyBy('impact_type'),
        ];

        $recentNetworkEvents = $occurredQ($app->networkMonitoringEvents())->latest('occurred_at')->limit(20)->get();
        $netQ = $occurredQ($app->networkMonitoringEvents());
        $networkStats = [
            'offline_sessions' => (clone $netQ)->where('event_type', 'offline_end')->count(),
            'sync_retries_total' => (clone $netQ)->where('event_type', 'sync_retry')->count(),
            'sync_retries_success' => (clone $netQ)->where('event_type', 'sync_retry')->where('success', true)->count(),
            'sync_retries_failed' => (clone $netQ)->where('event_type', 'sync_retry')->where('success', false)->count(),
            'by_strength' => (clone $netQ)->where('event_type', 'network_strength')->whereNotNull('network_strength')->selectRaw('network_strength, count(*) as count')->groupBy('network_strength')->pluck('count', 'network_strength'),
        ];

        $recentThirdPartyApiEvents = $occurredQ($app->thirdPartyApiEvents())->latest('occurred_at')->limit(20)->get();
        $tpQ = $occurredQ($app->thirdPartyApiEvents());
        $thirdPartyApiStats = [
            'total' => (clone $tpQ)->count(),
            'success_count' => (clone $tpQ)->where('success', true)->count(),
            'failure_count' => (clone $tpQ)->where('success', false)->count(),
            'by_provider_type' => (clone $tpQ)->selectRaw('provider_type, count(*) as count, sum(case when success then 1 else 0 end) as success_count')->groupBy('provider_type')->get()->keyBy('provider_type'),
            'avg_response_time_ms' => (int) (clone $tpQ)->whereNotNull('response_time_ms')->avg('response_time_ms'),
        ];

        $fuQ = $occurredQ($app->featureUsageEvents());
        $featureUsageStats = [
            'total' => (clone $fuQ)->count(),
            'most_used' => (clone $fuQ)->selectRaw('feature_name, feature_category, count(*) as count')->groupBy('feature_name', 'feature_category')->orderByDesc('count')->limit(15)->get(),
            'least_used' => (clone $fuQ)->selectRaw('feature_name, feature_category, count(*) as count')->groupBy('feature_name', 'feature_category')->orderBy('count')->limit(15)->get(),
        ];
        $recentFeatureUsage = $occurredQ($app->featureUsageEvents())->latest('occurred_at')->limit(15)->get();

        $moduleHealthScores = $recordedQ($app->moduleHealthScores())->latest('recorded_at')->limit(30)->get();
        $mhQ = $recordedQ($app->moduleHealthScores());
        $moduleHealthByModule = (clone $mhQ)->selectRaw('module_id, module_name, max(recorded_at) as latest_at')->groupBy('module_id', 'module_name')->get()->keyBy('module_id');
        $latestScoresByModule = [];
        foreach ($moduleHealthByModule as $mid => $row) {
            $latest = (clone $mhQ)->where('module_id', $mid)->latest('recorded_at')->first();
            if ($latest) {
                $latestScoresByModule[$mid] = $latest;
            }
        }

        $recentSecurityEvents = $occurredQ($app->securityEvents())->latest('occurred_at')->limit(25)->get();
        $securityEventStats = [
            'total' => $occurredQ($app->securityEvents())->count(),
            'by_type' => $occurredQ($app->securityEvents())->selectRaw('event_type, count(*) as count')->groupBy('event_type')->pluck('count', 'event_type'),
        ];

        $recentDataIntegrityEvents = $occurredQ($app->dataIntegrityEvents())->latest('occurred_at')->limit(25)->get();
        $dataIntegrityStats = [
            'total' => $occurredQ($app->dataIntegrityEvents())->count(),
            'by_type' => $occurredQ($app->dataIntegrityEvents())->selectRaw('event_type, count(*) as count')->groupBy('event_type')->pluck('count', 'event_type'),
        ];

        $recentAlerts = $occurredQ($app->alerts())->latest('occurred_at')->limit(25)->get();
        $alertStats = [
            'total' => $occurredQ($app->alerts())->count(),
            'unacknowledged' => $occurredQ($app->alerts())->whereNull('acknowledged_at')->count(),
            'by_type' => $occurredQ($app->alerts())->selectRaw('alert_type, count(*) as count')->groupBy('alert_type')->pluck('count', 'alert_type'),
        ];

        $platformModel = $this->sdkConfigService->getPlatform($app);
        $app->setRelation('platform', $platformModel);
        $sdkInstructions = $this->sdkConfigService->getInstallInstructions($app);

        // Platform feature support checks for dashboard adaptation
        $platformSupports = [
            'sessions' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'sessions'),
            'navigation' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'navigation'),
            'screen_views' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'screen_views'),
            'user_actions' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'user_actions'),
            'errors' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'errors'),
            'performance' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'performance'),
            'network_monitoring' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'network_monitoring'),
            'offline' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'offline'),
            'third_party_api' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'third_party_api'),
            'security_events' => $this->sdkConfigService->platformSupportsFeature($platformModel, 'security_events'),
        ];

        return view('apps.show', compact('app', 'platformModel', 'recentEvents', 'activeSessions', 'recentSessions', 'sessionStats', 'recentNavigations', 'recentScreenViews', 'navigationStats', 'screenViewStats', 'recentUserActions', 'recentErrors', 'userActionStats', 'errorStats', 'recentPerformanceMetrics', 'performanceStats', 'geoPerformance', 'journeySessions', 'recentBusinessEvents', 'businessEventStats', 'recentFinancialImpacts', 'financialImpactStats', 'recentNetworkEvents', 'networkStats', 'recentThirdPartyApiEvents', 'thirdPartyApiStats', 'featureUsageStats', 'recentFeatureUsage', 'moduleHealthScores', 'latestScoresByModule', 'recentSecurityEvents', 'securityEventStats', 'recentDataIntegrityEvents', 'dataIntegrityStats', 'recentAlerts', 'alertStats', 'filterEnv', 'filterDateFrom', 'filterDateTo', 'filterDatePreset', 'filterDateFromInput', 'filterDateToInput', 'sdkInstructions', 'platformSupports'));
    }

    /**
     * Show edit form.
     */
    public function edit(App $app): View
    {
        $platforms = Platform::active()->orderBy('sort_order')->get()->groupBy('category');
        return view('apps.edit', compact('app', 'platforms'));
    }

    /**
     * Update app.
     */
    public function update(Request $request, App $app): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9_-]+$/', Rule::unique('apps', 'slug')->ignore($app->id)],
            'description' => 'nullable|string|max:1000',
            'platform_id' => 'nullable|integer|exists:platforms,id',
            'platform' => 'nullable|string|max:50', // legacy fallback
            'is_tracking_enabled' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_tracking_enabled'] = $request->boolean('is_tracking_enabled', true);
        // Set legacy platform from platform_id if not provided
        if (isset($validated['platform_id']) && empty($validated['platform'])) {
            $platform = Platform::find($validated['platform_id']);
            $validated['platform'] = $platform?->slug ?? $app->platform;
        }

        $app->update($validated);

        return redirect()->route('apps.show', $app)
            ->with('success', 'App updated successfully.');
    }

    /**
     * Delete app.
     */
    public function destroy(App $app): RedirectResponse
    {
        $app->delete();
        return redirect()->route('apps.index')
            ->with('success', 'App deleted successfully.');
    }

    /**
     * Generate a new API key for the app.
     */
    public function storeApiKey(Request $request, App $app): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ['api_key' => $apiKey, 'raw_key' => $rawKey] = $this->apiKeyService->generate($app, $validated['name']);

        return redirect()->route('apps.show', $app)
            ->with('api_key_created', true)
            ->with('raw_api_key', $rawKey)
            ->with('api_key_name', $apiKey->name);
    }

    /**
     * Revoke an API key.
     */
    public function revokeApiKey(Request $request, App $app, ApiKey $api_key): RedirectResponse
    {
        if ($api_key->app_id !== $app->id) {
            abort(404);
        }
        $this->apiKeyService->revoke($api_key);
        return redirect()->route('apps.show', $app)
            ->with('success', 'API key revoked.');
    }

    /**
     * User journey: full navigation flow for a session (merged timeline).
     */
    public function journey(App $app, string $sessionId): View
    {
        $items = [];

        foreach ($app->journeyEvents()->where('session_id', $sessionId)->orderBy('occurred_at')->get() as $e) {
            $items[] = [
                'at' => $e->occurred_at,
                'type' => 'journey',
                'label' => $e->step_name,
                'detail' => $e->step_type,
                'payload' => $e->payload,
            ];
        }
        foreach ($app->screenViews()->where('session_id', $sessionId)->orderBy('occurred_at')->get() as $e) {
            $items[] = [
                'at' => $e->occurred_at,
                'type' => 'screen',
                'label' => $e->screen_name,
                'detail' => $e->previous_screen ? 'from ' . $e->previous_screen : null,
                'payload' => ['load_time_ms' => $e->load_time_ms],
            ];
        }
        foreach ($app->navigationEvents()->where('session_id', $sessionId)->orderBy('occurred_at')->get() as $e) {
            $items[] = [
                'at' => $e->occurred_at,
                'type' => 'navigation',
                'label' => $e->from_screen . ' → ' . $e->to_screen,
                'detail' => $e->navigation_type,
                'payload' => null,
            ];
        }
        foreach ($app->userActions()->where('session_id', $sessionId)->orderBy('occurred_at')->get() as $e) {
            $items[] = [
                'at' => $e->occurred_at,
                'type' => 'action',
                'label' => $e->action_name ?? $e->action_type,
                'detail' => $e->target,
                'payload' => $e->payload,
            ];
        }

        usort($items, fn ($a, $b) => $a['at'] <=> $b['at']);

        return view('journey.show', compact('app', 'sessionId', 'items'));
    }
}
