<?php

use App\Http\Controllers\Api\ErrorController;
use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\PerformanceMetricController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\UserActionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TraceNova Tracking API (for external apps)
|--------------------------------------------------------------------------
| All routes here require a valid API key (X-Api-Key or Authorization: Bearer).
*/

Route::prefix('v1')->middleware(['tracking.api.key', 'throttle:120,1'])->group(function () {
    Route::post('/track', [TrackController::class, 'store']);
    Route::post('/track/batch', [TrackController::class, 'storeBatch']);

    Route::prefix('sessions')->group(function () {
        Route::post('/start', [SessionController::class, 'start']);
        Route::post('/end', [SessionController::class, 'end']);
        Route::post('/heartbeat', [SessionController::class, 'heartbeat']);
    });

    Route::post('/navigation', [NavigationController::class, 'storeNavigation']);
    Route::post('/screen-views', [NavigationController::class, 'storeScreenView']);

    Route::post('/user-actions', [UserActionController::class, 'store']);
    Route::post('/errors', [ErrorController::class, 'store']);
    Route::post('/performance-metrics', [PerformanceMetricController::class, 'store']);
    Route::post('/funnel-steps', [\App\Http\Controllers\Api\FunnelController::class, 'storeStep']);
    Route::post('/journey', [\App\Http\Controllers\Api\JourneyController::class, 'store']);
    Route::post('/business-events', [\App\Http\Controllers\Api\BusinessEventController::class, 'store']);
    Route::post('/financial-impacts', [\App\Http\Controllers\Api\FinancialImpactController::class, 'store']);
    Route::post('/network-monitoring', [\App\Http\Controllers\Api\NetworkMonitoringController::class, 'store']);
    Route::post('/third-party-api', [\App\Http\Controllers\Api\ThirdPartyApiController::class, 'store']);
    Route::post('/feature-usage', [\App\Http\Controllers\Api\FeatureUsageController::class, 'store']);
    Route::post('/module-health', [\App\Http\Controllers\Api\ModuleHealthController::class, 'store']);
    Route::post('/security-events', [\App\Http\Controllers\Api\SecurityEventController::class, 'store']);
    Route::post('/data-integrity', [\App\Http\Controllers\Api\DataIntegrityController::class, 'store']);
    Route::post('/alerts', [\App\Http\Controllers\Api\AlertController::class, 'store']);
});
