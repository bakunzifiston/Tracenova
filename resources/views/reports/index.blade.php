<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('apps.show', $app) }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports Review</h2>
                    <p class="text-sm text-gray-500">{{ $app->name }} — Filter by environment and date range</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Filters</h3>
                <form method="get" action="{{ route('apps.reports.index', $app) }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Environment</label>
                        <select name="environment" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All</option>
                            @foreach (\App\Services\ReportFilterService::ENVIRONMENTS as $val => $label)
                                <option value="{{ $val }}" {{ $environment === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Date range</label>
                        <select name="date_preset" id="date_preset" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach (\App\Services\ReportFilterService::DATE_PRESETS as $val => $label)
                                <option value="{{ $val }}" {{ $datePreset === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="custom_dates" class="flex items-center gap-2 {{ $datePreset !== 'custom' ? 'hidden' : '' }}">
                        <input type="date" name="date_from" value="{{ $dateFromInput ?? $dateFrom?->format('Y-m-d') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                        <span class="text-gray-500">→</span>
                        <input type="date" name="date_to" value="{{ $dateToInput ?? $dateTo?->format('Y-m-d') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Apply</button>
                </form>
                <p class="text-sm text-gray-500 mt-2">Showing data from {{ $dateFrom?->format('M j, Y') }} to {{ $dateTo?->format('M j, Y') }}@if($environment) · Environment: {{ \App\Services\ReportFilterService::ENVIRONMENTS[$environment] ?? $environment }}@endif</p>
            </div>

            {{-- Overview cards (click to scroll to detail) --}}
            <div id="kpi-overview" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 scroll-mt-6">
                <a href="#detail-sessions" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-xs font-medium text-gray-500 group-hover:text-indigo-600 uppercase">Total sessions</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalSessions) }}</p>
                    <p class="mt-1 text-xs text-gray-400">View chart &amp; breakdown</p>
                </a>
                <a href="#detail-sessions" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-xs font-medium text-gray-500 group-hover:text-indigo-600 uppercase">Unique users</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($uniqueUsers ?? 0) }}</p>
                    <p class="mt-1 text-xs text-gray-400">Sessions with user_id</p>
                </a>
                <a href="#detail-errors" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-xs font-medium text-gray-500 group-hover:text-indigo-600 uppercase">Errors count</p>
                    <p class="mt-1 text-2xl font-semibold {{ $errorsCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($errorsCount) }}</p>
                    <p class="mt-1 text-xs text-gray-400">View chart &amp; error logs</p>
                </a>
                <a href="#detail-performance" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-xs font-medium text-gray-500 group-hover:text-indigo-600 uppercase">Avg performance time</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $avgPerformanceTime ? $avgPerformanceTime . ' ms' : '—' }}</p>
                    <p class="mt-1 text-xs text-gray-400">View trends &amp; slow screens</p>
                </a>
                <a href="#kpi-overview" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-xs font-medium text-gray-500 group-hover:text-indigo-600 uppercase">Revenue impact</p>
                    <p class="mt-1 text-2xl font-semibold {{ $revenueImpact > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $revenueImpact ? number_format($revenueImpact, 2) . ' USD' : '—' }}</p>
                    <p class="mt-1 text-xs text-gray-400">From financial impacts</p>
                </a>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div id="detail-errors" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 scroll-mt-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Errors over time</h4>
                    <div class="h-48 flex items-end gap-0.5">
                        @php $maxErr = $errorsOverTime->max('count') ?: 1; @endphp
                        @foreach ($days as $day)
                            @php $count = $errorsOverTime->get($day)?->count ?? 0; $pct = $maxErr ? round($count / $maxErr * 100) : 0; @endphp
                            <div class="flex-1 flex flex-col items-center" title="{{ $day }}: {{ $count }}">
                                <span class="w-full bg-red-500 rounded-t min-h-[2px]" style="height: {{ max(2, $pct) }}%"></span>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-2">By day (hover for count)</p>
                </div>
                <div id="detail-sessions" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 scroll-mt-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Sessions per day</h4>
                    <div class="h-48 flex items-end gap-0.5">
                        @php $maxSess = $sessionsPerDay->max('count') ?: 1; @endphp
                        @foreach ($days as $day)
                            @php $count = $sessionsPerDay->get($day)?->count ?? 0; $pct = $maxSess ? round($count / $maxSess * 100) : 0; @endphp
                            <div class="flex-1 flex flex-col items-center" title="{{ $day }}: {{ $count }}">
                                <span class="w-full bg-indigo-500 rounded-t min-h-[2px]" style="height: {{ max(2, $pct) }}%"></span>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-2">By day</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div id="detail-performance" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 scroll-mt-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Performance trends (avg ms)</h4>
                    <div class="h-48 flex items-end gap-0.5">
                        @php $maxPerf = $performanceTrends->max('avg_value') ?: 1; @endphp
                        @foreach ($days as $day)
                            @php $avg = $performanceTrends->get($day)?->avg_value ?? 0; $pct = $maxPerf ? min(100, round($avg / $maxPerf * 100)) : 0; @endphp
                            <div class="flex-1 flex flex-col items-center" title="{{ $day }}: {{ round($avg) }} ms">
                                <span class="w-full bg-amber-500 rounded-t min-h-[2px]" style="height: {{ max(2, $pct) }}%"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Environment comparison</h4>
                    @if ($environmentComparison->isNotEmpty())
                        <ul class="space-y-2">
                            @foreach ($environmentComparison as $row)
                                <li class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-700">{{ \App\Services\ReportFilterService::ENVIRONMENTS[$row->environment] ?? $row->environment ?? '—' }}</span>
                                    <span class="text-gray-600">{{ number_format($row->count) }} sessions</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">No session data in this range.</p>
                    @endif
                </div>
            </div>

            {{-- Tables --}}
            <div id="detail-error-logs" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Filtered error logs</h3>
                    <p class="text-sm text-gray-500">Last 50 errors in selected range</p>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    @if ($filteredErrorLogs->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Environment</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($filteredErrorLogs as $e)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-800">{{ Str::limit($e->message, 60) }}</td>
                                        <td class="px-3 py-2"><span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">{{ $e->severity }}</span></td>
                                        <td class="px-3 py-2 text-gray-600">{{ $e->environment ?? '—' }}</td>
                                        <td class="px-3 py-2 text-gray-500">{{ $e->occurred_at?->format('M j, H:i') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="p-6 text-sm text-gray-500">No errors in selected range.</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Slow performance screens</h3>
                    </div>
                    @if ($slowPerformanceScreens->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Screen / Name</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Count</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Avg (ms)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($slowPerformanceScreens as $s)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-800">{{ $s->name ?? '—' }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ number_format($s->count) }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ round($s->avg_value) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="p-6 text-sm text-gray-500">No slow performance in selected range.</p>
                    @endif
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Security incidents</h3>
                    </div>
                    @if ($securityIncidents->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($securityIncidents as $s)
                                    <tr>
                                        <td class="px-3 py-2"><span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">{{ $s->event_type }}</span></td>
                                        <td class="px-3 py-2 text-gray-600">{{ Str::limit($s->reason ?? '—', 40) }}</td>
                                        <td class="px-3 py-2 text-gray-500">{{ $s->occurred_at?->format('M j, H:i') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="p-6 text-sm text-gray-500">No security incidents in selected range.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>html { scroll-behavior: smooth; }</style>
    <script>
        document.getElementById('date_preset')?.addEventListener('change', function() {
            document.getElementById('custom_dates').classList.toggle('hidden', this.value !== 'custom');
        });
    </script>
</x-app-layout>
