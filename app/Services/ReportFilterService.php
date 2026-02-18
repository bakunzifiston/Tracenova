<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportFilterService
{
    /** Environment values for filtering. Use "development" when app runs locally, "production" when live. */
    public const ENVIRONMENTS = [
        'production' => 'Production (live)',
        'development' => 'Development (local)',
        'staging' => 'Staging',
        'testing' => 'Testing',
    ];

    public const DATE_PRESETS = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 days',
        'last_30_days' => 'Last 30 days',
        'custom' => 'Custom range',
    ];

    /**
     * Get environment from request (query or input). Default: all (null).
     */
    public static function environmentFromRequest(Request $request): ?string
    {
        $env = $request->query('environment') ?? $request->input('environment');
        if ($env === '' || $env === 'all') {
            return null;
        }
        if (in_array($env, array_keys(self::ENVIRONMENTS), true)) {
            return $env;
        }
        return null;
    }

    /**
     * Get [start, end] Carbon dates from request.
     * Presets: today, yesterday, last_7_days, last_30_days.
     * Custom: date_from, date_to (Y-m-d).
     */
    public static function dateRangeFromRequest(Request $request): array
    {
        $preset = $request->query('date_preset') ?? $request->input('date_preset', 'last_30_days');
        $tz = config('app.timezone', 'UTC');

        switch ($preset) {
            case 'today':
                $start = Carbon::today($tz)->startOfDay();
                $end = Carbon::today($tz)->endOfDay();
                break;
            case 'yesterday':
                $start = Carbon::yesterday($tz)->startOfDay();
                $end = Carbon::yesterday($tz)->endOfDay();
                break;
            case 'last_7_days':
                $end = Carbon::today($tz)->endOfDay();
                $start = Carbon::today($tz)->subDays(6)->startOfDay();
                break;
            case 'last_30_days':
                $end = Carbon::today($tz)->endOfDay();
                $start = Carbon::today($tz)->subDays(29)->startOfDay();
                break;
            case 'custom':
                $from = $request->query('date_from') ?? $request->input('date_from');
                $to = $request->query('date_to') ?? $request->input('date_to');
                if ($from && $to) {
                    $start = Carbon::parse($from, $tz)->startOfDay();
                    $end = Carbon::parse($to, $tz)->endOfDay();
                } else {
                    $end = Carbon::today($tz)->endOfDay();
                    $start = Carbon::today($tz)->subDays(29)->startOfDay();
                }
                break;
            default:
                $end = Carbon::today($tz)->endOfDay();
                $start = Carbon::today($tz)->subDays(29)->startOfDay();
        }

        return [$start, $end];
    }

    public static function applyEnvironment($query, ?string $environment, string $column = 'environment'): void
    {
        if ($environment !== null) {
            $query->where($column, $environment);
        }
    }

    /** Apply date range to a query on occurred_at. */
    public static function applyOccurredAtRange($query, Carbon $start, Carbon $end): void
    {
        $query->whereBetween('occurred_at', [$start, $end]);
    }

    /** Apply date range to a query on started_at (sessions). */
    public static function applyStartedAtRange($query, Carbon $start, Carbon $end): void
    {
        $query->whereBetween('started_at', [$start, $end]);
    }

    /** Apply date range to recorded_at (e.g. module_health_scores). */
    public static function applyRecordedAtRange($query, Carbon $start, Carbon $end): void
    {
        $query->whereBetween('recorded_at', [$start, $end]);
    }
}
