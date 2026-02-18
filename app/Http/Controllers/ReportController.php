<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Services\ReportFilterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Reports Review: overview cards, charts, tables with environment and date range filters.
     */
    public function index(Request $request, App $app): View
    {
        $environment = ReportFilterService::environmentFromRequest($request);
        [$dateFrom, $dateTo] = ReportFilterService::dateRangeFromRequest($request);
        $datePreset = $request->query('date_preset', 'last_30_days');
        $dateFromInput = $request->query('date_from');
        $dateToInput = $request->query('date_to');

        $baseSessions = $app->trackingSessions()->when($environment, fn ($q) => $q->where('environment', $environment))->whereBetween('started_at', [$dateFrom, $dateTo]);
        $baseErrors = $app->errorEvents()->when($environment, fn ($q) => $q->where('environment', $environment))->whereBetween('occurred_at', [$dateFrom, $dateTo]);
        $basePerformance = $app->performanceMetrics()->when($environment, fn ($q) => $q->where('environment', $environment))->whereBetween('occurred_at', [$dateFrom, $dateTo]);
        $baseFinancial = $app->financialImpacts()->when($environment, fn ($q) => $q->where('environment', $environment))->whereBetween('occurred_at', [$dateFrom, $dateTo]);
        $baseSecurity = $app->securityEvents()->when($environment, fn ($q) => $q->where('environment', $environment))->whereBetween('occurred_at', [$dateFrom, $dateTo]);

        // Overview cards
        $totalSessions = $baseSessions->count();
        $uniqueUsers = (int) (clone $baseSessions)->whereNotNull('user_id')->selectRaw('count(distinct user_id) as cnt')->value('cnt');
        $errorsCount = $baseErrors->count();
        $avgPerformanceTime = (int) (clone $basePerformance)->avg('value');
        $revenueImpact = (float) (clone $baseFinancial)->sum('amount');

        // Charts data: errors over time (by day)
        $errorsOverTime = (clone $baseErrors)
            ->selectRaw('DATE(occurred_at) as day, count(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Performance trends (avg value by day)
        $performanceTrends = (clone $basePerformance)
            ->selectRaw('DATE(occurred_at) as day, avg(value) as avg_value, count(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Sessions per day
        $sessionsPerDay = (clone $baseSessions)
            ->selectRaw('DATE(started_at) as day, count(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Environment comparison (sessions per environment in range)
        $environmentComparison = $app->trackingSessions()
            ->whereBetween('started_at', [$dateFrom, $dateTo])
            ->selectRaw('environment, count(*) as count')
            ->groupBy('environment')
            ->orderByDesc('count')
            ->get();

        // Tables: filtered error logs, slow performance screens, security incidents
        $filteredErrorLogs = (clone $baseErrors)->latest('occurred_at')->limit(50)->get();
        $slowPerformanceScreens = (clone $basePerformance)->where('is_slow', true)->selectRaw('name, count(*) as count, avg(value) as avg_value')->groupBy('name')->orderByDesc('count')->limit(20)->get();
        $securityIncidents = (clone $baseSecurity)->latest('occurred_at')->limit(30)->get();

        $days = [];
        $d = Carbon::parse($dateFrom)->copy();
        while ($d->lte($dateTo)) {
            $days[] = $d->format('Y-m-d');
            $d->addDay();
        }

        return view('reports.index', compact(
            'app',
            'environment',
            'dateFrom',
            'dateTo',
            'datePreset',
            'dateFromInput',
            'dateToInput',
            'totalSessions',
            'uniqueUsers',
            'errorsCount',
            'avgPerformanceTime',
            'revenueImpact',
            'errorsOverTime',
            'performanceTrends',
            'sessionsPerDay',
            'environmentComparison',
            'filteredErrorLogs',
            'slowPerformanceScreens',
            'securityIncidents',
            'days'
        ));
    }
}
