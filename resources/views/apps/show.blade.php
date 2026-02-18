<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('apps.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $app->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $app->slug }} · {{ (isset($platformModel) && $platformModel ? $platformModel->name : null) ?? (\App\Models\App::PLATFORMS[$app->platform] ?? $app->platform ?? 'Unknown') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if ($app->is_tracking_enabled)
                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">Tracking on</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Tracking off</span>
                @endif
                <a href="{{ route('apps.edit', $app) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Edit</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
            @endif

            {{-- Environment & date range filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex flex-wrap items-end gap-4">
                    <form method="get" action="{{ route('apps.show', $app) }}" class="flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Environment</label>
                            <select name="environment" class="rounded-md border-gray-300 shadow-sm text-sm w-40">
                                <option value="">All</option>
                                @foreach (\App\Services\ReportFilterService::ENVIRONMENTS as $val => $label)
                                    <option value="{{ $val }}" {{ ($filterEnv ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Date range</label>
                            <select name="date_preset" id="date_preset_app" class="rounded-md border-gray-300 shadow-sm text-sm w-40">
                                @foreach (\App\Services\ReportFilterService::DATE_PRESETS as $val => $label)
                                    <option value="{{ $val }}" {{ ($filterDatePreset ?? 'last_30_days') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="custom_dates_app" class="flex items-center gap-2 {{ ($filterDatePreset ?? '') !== 'custom' ? 'hidden' : '' }}">
                            <input type="date" name="date_from" value="{{ $filterDateFromInput ?? $filterDateFrom?->format('Y-m-d') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                            <span class="text-gray-500">→</span>
                            <input type="date" name="date_to" value="{{ $filterDateToInput ?? $filterDateTo?->format('Y-m-d') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Apply</button>
                    </form>
                    <a href="{{ route('apps.reports.index', $app) }}?environment={{ urlencode($filterEnv ?? '') }}&date_preset={{ urlencode($filterDatePreset ?? 'last_30_days') }}&date_from={{ urlencode($filterDateFromInput ?? '') }}&date_to={{ urlencode($filterDateToInput ?? '') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reports Review →</a>
                </div>
                @if(isset($filterDateFrom) && isset($filterDateTo))
                    <p class="text-xs text-gray-500 mt-2">Showing {{ $filterDateFrom->format('M j, Y') }} – {{ $filterDateTo->format('M j, Y') }}@if($filterEnv ?? null) · {{ \App\Services\ReportFilterService::ENVIRONMENTS[$filterEnv] ?? $filterEnv }}@endif</p>
                @endif
            </div>

            {{-- One-time display of new API key --}}
            @if (session('api_key_created') && session('raw_api_key'))
                <div x-data="{ show: true }" x-show="show" class="rounded-lg border-2 border-amber-200 bg-amber-50 p-4">
                    <p class="font-medium text-amber-900">New API key created: {{ session('api_key_name') }}</p>
                    <p class="mt-2 text-sm text-amber-800">Copy this key now. It won't be shown again.</p>
                    <div class="mt-3 flex items-center gap-2">
                        <code id="raw-key" class="flex-1 rounded bg-amber-100 px-3 py-2 text-sm font-mono break-all">{{ session('raw_api_key') }}</code>
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('raw-key').innerText)" class="rounded-md bg-amber-200 px-3 py-2 text-sm font-medium text-amber-900 hover:bg-amber-300">Copy</button>
                        <button type="button" @click="show = false" class="rounded-md bg-amber-200 px-3 py-2 text-sm font-medium text-amber-900 hover:bg-amber-300">Dismiss</button>
                    </div>
                </div>
            @endif

            {{-- Stats (click to scroll to detail section) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#section-api-keys" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-sm font-medium text-gray-500 group-hover:text-indigo-600">API keys</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $app->apiKeys->count() }}</p>
                    <p class="mt-1 text-xs text-gray-400">View keys &amp; generate new</p>
                </a>
                <a href="#section-recent-events" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-sm font-medium text-gray-500 group-hover:text-indigo-600">Total events</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($app->tracking_events_count) }}</p>
                    <p class="mt-1 text-xs text-gray-400">View recent event log</p>
                </a>
                <a href="#section-session-tracking" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-sm font-medium text-gray-500 group-hover:text-indigo-600">Unique users</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($sessionStats['unique_users'] ?? 0) }}</p>
                    <p class="mt-1 text-xs text-gray-400">Sessions with <code class="bg-gray-100 px-1">user_id</code> in selected period</p>
                </a>
                <a href="#section-api-keys" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:ring-2 hover:ring-indigo-500 hover:shadow-md transition-all cursor-pointer group">
                    <p class="text-sm font-medium text-gray-500 group-hover:text-indigo-600">Tracking</p>
                    <p class="mt-1 text-lg font-semibold {{ $app->is_tracking_enabled ? 'text-green-600' : 'text-gray-500' }}">{{ $app->is_tracking_enabled ? 'Enabled' : 'Disabled' }}</p>
                    <p class="mt-1 text-xs text-gray-400">Manage API keys</p>
                </a>
            </div>

            {{-- Session stats --}}
            @if(!isset($platformSupports) || $platformSupports['sessions'] ?? true)
            <div id="section-session-tracking" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Session tracking</h3>
                    <p class="text-sm text-gray-500 mt-1">Active sessions (activity in last {{ \App\Models\TrackingSession::ACTIVE_THRESHOLD_MINUTES }} min), duration, and foreground vs background time.</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 p-6">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Active now</p>
                        <p class="mt-1 text-2xl font-semibold text-green-600">{{ $sessionStats['active_count'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Total sessions</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($sessionStats['total']) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Avg duration</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $sessionStats['avg_duration_seconds'] ? gmdate('i:s', $sessionStats['avg_duration_seconds']) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Foreground time</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $sessionStats['total_foreground_seconds'] ? gmdate('H:i:s', $sessionStats['total_foreground_seconds']) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Background time</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $sessionStats['total_background_seconds'] ? gmdate('H:i:s', $sessionStats['total_background_seconds']) : '—' }}</p>
                    </div>
                </div>
                @if ($activeSessions->isNotEmpty())
                    <div class="px-6 pb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Active sessions</p>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Session ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Started</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Last activity</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Foreground / Background</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($activeSessions as $s)
                                        <tr>
                                            <td class="px-4 py-2 font-mono text-gray-900">{{ Str::limit($s->session_id, 24) }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $s->user_id ? Str::limit($s->user_id, 16) : '—' }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $s->started_at->diffForHumans() }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $s->last_activity_at ? $s->last_activity_at->diffForHumans() : '—' }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ gmdate('i:s', $s->foreground_seconds) }} / {{ gmdate('i:s', $s->background_seconds) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @if ($recentSessions->isNotEmpty())
                    <div class="px-6 pb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Recent ended sessions</p>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Session ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Foreground</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Background</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ended</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentSessions as $s)
                                        <tr>
                                            <td class="px-4 py-2 font-mono text-gray-900">{{ Str::limit($s->session_id, 24) }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $s->duration_seconds ? gmdate('i:s', $s->duration_seconds) : '—' }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ gmdate('i:s', $s->foreground_seconds) }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ gmdate('i:s', $s->background_seconds) }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $s->ended_at?->diffForHumans() ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            {{-- Navigation & screen view tracking --}}
            @if((!isset($platformSupports) || $platformSupports['navigation'] ?? true) || (!isset($platformSupports) || $platformSupports['screen_views'] ?? true))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @if(!isset($platformSupports) || $platformSupports['navigation'] ?? true)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Navigation tracking</h3>
                        <p class="text-sm text-gray-500 mt-1">From screen → to screen, with type (push, replace, back).</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Total</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($navigationStats['total']) }}</p>
                            </div>
                            @foreach (\App\Models\NavigationEvent::TYPES as $type => $label)
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($navigationStats['by_type'][$type] ?? 0) }}</p>
                                </div>
                            @endforeach
                        </div>
                        @if ($recentNavigations->isNotEmpty())
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">To</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentNavigations as $n)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-900">{{ Str::limit($n->from_screen, 20) }}</td>
                                                <td class="px-3 py-2 text-gray-900">{{ Str::limit($n->to_screen, 20) }}</td>
                                                <td class="px-3 py-2"><span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $n->navigation_type }}</span></td>
                                                <td class="px-3 py-2 text-gray-500">{{ $n->occurred_at?->diffForHumans() ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No navigation events yet.</p>
                        @endif
                    </div>
                </div>
                @endif
                @if(!isset($platformSupports) || $platformSupports['screen_views'] ?? true)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Screen view tracking</h3>
                        <p class="text-sm text-gray-500 mt-1">Screen name, previous screen, timestamp, load time.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Total views</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($screenViewStats['total']) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Avg load time</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900">{{ $screenViewStats['avg_load_time_ms'] ? $screenViewStats['avg_load_time_ms'] . ' ms' : '—' }}</p>
                            </div>
                        </div>
                        @if ($screenViewStats['top_screens']->isNotEmpty())
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Top screens</p>
                            <ul class="space-y-1 text-sm mb-4">
                                @foreach ($screenViewStats['top_screens'] as $t)
                                    <li class="flex justify-between"><span class="text-gray-700">{{ Str::limit($t->screen_name, 28) }}</span><span class="text-gray-500">{{ number_format($t->views) }} views</span></li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($recentScreenViews->isNotEmpty())
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Screen</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Previous</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Load</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentScreenViews as $v)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-900">{{ Str::limit($v->screen_name, 18) }}</td>
                                                <td class="px-3 py-2 text-gray-600">{{ Str::limit($v->previous_screen ?? '—', 18) }}</td>
                                                <td class="px-3 py-2 text-gray-600">{{ $v->load_time_ms !== null ? $v->load_time_ms . ' ms' : '—' }}</td>
                                                <td class="px-3 py-2 text-gray-500">{{ $v->occurred_at?->diffForHumans() ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No screen views yet.</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- User actions & Error tracking --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @if(!isset($platformSupports) || $platformSupports['user_actions'] ?? true)
                <div id="section-user-actions" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">User action tracking</h3>
                        <p class="text-sm text-gray-500 mt-1">Button clicks, dashboard access, payments, form submissions.</p>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-4 mb-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Total</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($userActionStats['total']) }}</p>
                            </div>
                            @foreach (\App\Models\UserAction::TYPES as $type => $label)
                                @if (($userActionStats['by_type'][$type] ?? 0) > 0)
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($userActionStats['by_type'][$type] ?? 0) }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @if ($recentUserActions->isNotEmpty())
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action / Target</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentUserActions as $a)
                                            <tr>
                                                <td class="px-3 py-2"><span class="inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $a->action_type }}</span></td>
                                                <td class="px-3 py-2 text-gray-900">{{ Str::limit($a->action_name ?? $a->target ?? '—', 24) }}</td>
                                                <td class="px-3 py-2 text-gray-500">{{ $a->occurred_at?->diffForHumans() ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No user actions yet.</p>
                        @endif
                    </div>
                </div>
                @endif
                @if(!isset($platformSupports) || $platformSupports['errors'] ?? true)
                <div id="section-error-tracking" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Error tracking</h3>
                        <p class="text-sm text-gray-500 mt-1">Error message, stack trace, file & line, user & device info, severity.</p>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-4 mb-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Total errors</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($errorStats['total']) }}</p>
                            </div>
                            @foreach (\App\Models\ErrorEvent::SEVERITIES as $sev => $label)
                                @if (($errorStats['by_severity'][$sev] ?? 0) > 0)
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                        <p class="mt-1 text-lg font-semibold {{ $sev === 'critical' || $sev === 'error' ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($errorStats['by_severity'][$sev] ?? 0) }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @if ($recentErrors->isNotEmpty())
                            <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-80 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentErrors as $e)
                                            <tr>
                                                <td class="px-3 py-2">
                                                    @php
                                                        $severityClass = match($e->severity) {
                                                            'critical' => 'bg-red-100 text-red-800',
                                                            'error' => 'bg-red-50 text-red-700',
                                                            'warning' => 'bg-amber-100 text-amber-800',
                                                            'info' => 'bg-blue-100 text-blue-800',
                                                            default => 'bg-gray-100 text-gray-700',
                                                        };
                                                    @endphp
                                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $severityClass }}">{{ $e->severity }}</span>
                                                </td>
                                                <td class="px-3 py-2 text-gray-900 max-w-xs truncate" title="{{ $e->message }}">{{ Str::limit($e->message, 40) }}</td>
                                                <td class="px-3 py-2 text-gray-500 text-xs">{{ $e->file ? Str::limit(basename($e->file), 16) . ($e->line ? ':'.$e->line : '') : '—' }}</td>
                                                <td class="px-3 py-2 text-gray-500">{{ $e->occurred_at?->diffForHumans() ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No errors recorded yet.</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Performance monitoring --}}
            @if(!isset($platformSupports) || $platformSupports['performance'] ?? true)
            <div id="section-performance" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Performance monitoring</h3>
                    <p class="text-sm text-gray-500 mt-1">Screen load time, API response time, session duration. Flag slow performance with <code class="rounded bg-gray-100 px-1">is_slow</code>.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total metrics</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($performanceStats['total']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Slow flagged</p>
                            <p class="mt-1 text-xl font-semibold {{ $performanceStats['slow_count'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ number_format($performanceStats['slow_count']) }}</p>
                        </div>
                        @foreach (\App\Models\PerformanceMetric::TYPES as $type => $label)
                            @php
                                $byType = $performanceStats['by_type'][$type] ?? null;
                                $unit = $type === \App\Models\PerformanceMetric::TYPE_SESSION_DURATION ? 's' : 'ms';
                            @endphp
                            @if ($byType && $byType->count > 0)
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($byType->count) }} <span class="text-gray-500 font-normal text-sm">(avg {{ $unit === 's' ? round($byType->avg_value, 1) . ' s' : (int) $byType->avg_value . ' ms' }})</span></p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if ($recentPerformanceMetrics->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-80 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Slow</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentPerformanceMetrics as $m)
                                        <tr class="{{ $m->is_slow ? 'bg-amber-50' : '' }}">
                                            <td class="px-3 py-2"><span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $m->metric_type }}</span></td>
                                            <td class="px-3 py-2 text-gray-900">{{ Str::limit($m->name ?? '—', 24) }}</td>
                                            <td class="px-3 py-2 text-gray-700">{{ $m->formatted_value }}{{ $m->threshold ? ' (≥ ' . $m->threshold . ' ' . $m->value_unit . ')' : '' }}</td>
                                            <td class="px-3 py-2 text-gray-500 text-xs">{{ $m->city ? $m->city . ', ' . ($m->region ? $m->region . ', ' : '') . ($m->country ?? $m->country_code ?? '—') : ($m->country ?? $m->country_code ?? '—') }}</td>
                                            <td class="px-3 py-2">
                                                @if ($m->is_slow)
                                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Slow</span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-gray-500">{{ $m->occurred_at?->diffForHumans() ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No performance metrics yet. Send screen_load, api_response, or session_duration with <code class="rounded bg-gray-100 px-1">POST /api/v1/performance-metrics</code>.</p>
                    @endif
                    @if ($geoPerformance['by_country']->isNotEmpty() || $geoPerformance['by_region']->isNotEmpty() || $geoPerformance['by_city']->isNotEmpty())
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-3">Geo-Performance (by country, region, city)</h4>
                            <p class="text-sm text-gray-500 mb-4">Send <code class="rounded bg-gray-100 px-1">country_code</code>, <code class="rounded bg-gray-100 px-1">country</code>, <code class="rounded bg-gray-100 px-1">region</code>, <code class="rounded bg-gray-100 px-1">city</code> with performance-metrics to see breakdown by location.</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @if ($geoPerformance['by_country']->isNotEmpty())
                                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="px-3 py-2 bg-gray-50 text-xs font-medium text-gray-500 uppercase">By country</div>
                                        <ul class="divide-y divide-gray-100 max-h-48 overflow-y-auto">
                                            @foreach ($geoPerformance['by_country'] as $r)
                                                <li class="px-3 py-2 flex justify-between text-sm">
                                                    <span class="text-gray-700">{{ $r->country ?? $r->country_code ?? '—' }}</span>
                                                    <span class="text-gray-500">{{ number_format($r->count) }} · avg {{ (int) $r->avg_value }} ms</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if ($geoPerformance['by_region']->isNotEmpty())
                                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="px-3 py-2 bg-gray-50 text-xs font-medium text-gray-500 uppercase">By region</div>
                                        <ul class="divide-y divide-gray-100 max-h-48 overflow-y-auto">
                                            @foreach ($geoPerformance['by_region'] as $r)
                                                <li class="px-3 py-2 flex justify-between text-sm">
                                                    <span class="text-gray-700">{{ $r->region }}{{ $r->country ? ', ' . $r->country : '' }}</span>
                                                    <span class="text-gray-500">{{ number_format($r->count) }} · avg {{ (int) $r->avg_value }} ms</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if ($geoPerformance['by_city']->isNotEmpty())
                                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="px-3 py-2 bg-gray-50 text-xs font-medium text-gray-500 uppercase">By city</div>
                                        <ul class="divide-y divide-gray-100 max-h-48 overflow-y-auto">
                                            @foreach ($geoPerformance['by_city'] as $r)
                                                <li class="px-3 py-2 flex justify-between text-sm">
                                                    <span class="text-gray-700">{{ $r->city }}{{ $r->country ? ', ' . $r->country : '' }}</span>
                                                    <span class="text-gray-500">{{ number_format($r->count) }} · avg {{ (int) $r->avg_value }} ms</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Business event tracking --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Business event tracking</h3>
                    <p class="text-sm text-gray-500 mt-1">Orders created, payments completed, inventory updates, product requests.</p>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total events</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($businessEventStats['total']) }}</p>
                        </div>
                        @foreach (\App\Models\BusinessEvent::TYPES as $type => $label)
                            @if (($businessEventStats['by_type'][$type] ?? 0) > 0)
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($businessEventStats['by_type'][$type]) }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if ($recentBusinessEvents->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Payload</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentBusinessEvents as $e)
                                        <tr>
                                            <td class="px-3 py-2"><span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">{{ $e->event_type }}</span></td>
                                            <td class="px-3 py-2 text-gray-600 font-mono">{{ Str::limit($e->reference_id ?? '—', 20) }}</td>
                                            <td class="px-3 py-2 text-gray-600 max-w-xs truncate" title="{{ json_encode($e->payload) }}">{{ Str::limit(json_encode($e->payload), 40) }}</td>
                                            <td class="px-3 py-2 text-gray-500">{{ $e->occurred_at?->diffForHumans() ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No business events yet. Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/business-events</code> (event_type: order_created, payment_completed, inventory_update, product_request, custom).</p>
                    @endif
                </div>
            </div>

            {{-- Financial impact monitoring --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Financial impact monitoring</h3>
                    <p class="text-sm text-gray-500 mt-1">Revenue impact of failed payments, system errors, and downtime.</p>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-6 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total impact</p>
                            <p class="mt-1 text-2xl font-semibold {{ $financialImpactStats['total_amount'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($financialImpactStats['total_amount'], 2) }} <span class="text-sm font-normal text-gray-500">USD</span></p>
                        </div>
                        @foreach (\App\Models\FinancialImpact::TYPES as $type => $label)
                            @php $byType = $financialImpactStats['by_type'][$type] ?? null; @endphp
                            @if ($byType && ($byType->count > 0 || (float)($byType->total ?? 0) > 0))
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($byType->count) }} events · {{ number_format((float) $byType->total, 2) }} USD</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if ($recentFinancialImpacts->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentFinancialImpacts as $i)
                                        <tr>
                                            <td class="px-3 py-2"><span class="inline-flex rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ $i->impact_type }}</span></td>
                                            <td class="px-3 py-2 font-semibold text-red-600">{{ number_format($i->amount, 2) }} {{ $i->currency }}</td>
                                            <td class="px-3 py-2 text-gray-600 font-mono">{{ Str::limit($i->reference_id ?? $i->description ?? '—', 24) }}</td>
                                            <td class="px-3 py-2 text-gray-500">{{ $i->occurred_at?->diffForHumans() ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No financial impacts recorded yet. Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/financial-impacts</code> (impact_type: failed_payment, system_error, downtime) and amount.</p>
                    @endif
                </div>
            </div>

            {{-- Offline & network monitoring --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Offline & network monitoring</h3>
                    <p class="text-sm text-gray-500 mt-1">Offline sessions, sync retries, and network strength.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Offline sessions</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($networkStats['offline_sessions']) }}</p>
                            <p class="text-xs text-gray-500">offline_end events</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Sync retries</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($networkStats['sync_retries_total']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Sync success</p>
                            <p class="mt-1 text-xl font-semibold text-green-600">{{ number_format($networkStats['sync_retries_success']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Sync failed</p>
                            <p class="mt-1 text-xl font-semibold {{ $networkStats['sync_retries_failed'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($networkStats['sync_retries_failed']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Strength samples</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($networkStats['by_strength']->sum()) }}</p>
                        </div>
                    </div>
                    @if ($networkStats['by_strength']->isNotEmpty())
                        <div class="flex flex-wrap gap-3 mb-4">
                            @foreach ($networkStats['by_strength'] as $strength => $count)
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $strength === 'weak' ? 'bg-red-100 text-red-800' : ($strength === 'strong' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800') }}">{{ $strength }}: {{ number_format($count) }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if ($recentNetworkEvents->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-64 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentNetworkEvents as $e)
                                        <tr>
                                            <td class="px-3 py-2"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $e->event_type }}</span></td>
                                            <td class="px-3 py-2 text-gray-600">
                                                @if ($e->event_type === 'offline_end' && $e->duration_seconds) {{ $e->duration_seconds }}s offline
                                                @elseif ($e->event_type === 'sync_retry') Retry #{{ $e->retry_count }} · {{ $e->success ? 'OK' : 'Failed' }}
                                                @elseif ($e->event_type === 'network_strength') {{ $e->network_strength ?? '—' }}
                                                @else — @endif
                                            </td>
                                            <td class="px-3 py-2 text-gray-500">{{ $e->occurred_at?->diffForHumans() ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No offline/network events yet. Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/network-monitoring</code> (event_type: offline_start, offline_end, sync_retry, network_strength).</p>
                    @endif
                </div>
            </div>

            {{-- Third-Party API monitoring --}}
            @if(!isset($platformSupports) || $platformSupports['third_party_api'] ?? true)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Third-Party API monitoring</h3>
                    <p class="text-sm text-gray-500 mt-1">Payment, SMS, and email provider health: success/failure, response time, status codes.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total calls</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($thirdPartyApiStats['total']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Success</p>
                            <p class="mt-1 text-xl font-semibold text-green-600">{{ number_format($thirdPartyApiStats['success_count']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Failures</p>
                            <p class="mt-1 text-xl font-semibold {{ $thirdPartyApiStats['failure_count'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($thirdPartyApiStats['failure_count']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Avg response</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $thirdPartyApiStats['avg_response_time_ms'] ? $thirdPartyApiStats['avg_response_time_ms'] . ' ms' : '—' }}</p>
                        </div>
                    </div>
                    @foreach (\App\Models\ThirdPartyApiEvent::TYPES as $pt => $label)
                        @php $byType = $thirdPartyApiStats['by_provider_type'][$pt] ?? null; @endphp
                        @if ($byType && $byType->count > 0)
                            <div class="flex flex-wrap gap-3 mb-2">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-slate-100 text-slate-800">{{ $label }}: {{ number_format($byType->count) }} ({{ number_format($byType->success_count ?? 0) }} ok)</span>
                            </div>
                        @endif
                    @endforeach
                    @if ($recentThirdPartyApiEvents->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-64 overflow-y-auto mt-4">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Operation</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time / Latency</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentThirdPartyApiEvents as $e)
                                        <tr>
                                            <td class="px-3 py-2"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ \App\Models\ThirdPartyApiEvent::TYPES[$e->provider_type] ?? $e->provider_type }}</span></td>
                                            <td class="px-3 py-2 text-gray-700">{{ $e->provider_name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ $e->operation ?? '—' }}</td>
                                            <td class="px-3 py-2">
                                                @if ($e->success)
                                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">OK</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">{{ $e->status_code ?? 'Error' }}</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-gray-500">{{ $e->occurred_at?->diffForHumans() ?? '—' }} @if($e->response_time_ms) · {{ $e->response_time_ms }} ms @endif</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mt-4">No third-party API events yet. Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/third-party-api</code> (provider_type: payment, sms, email; success, response_time_ms, status_code).</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Feature usage analytics --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Feature usage analytics</h3>
                    <p class="text-sm text-gray-500 mt-1">Most and least used features across the app.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Most used features</p>
                            @if ($featureUsageStats['most_used']->isNotEmpty())
                                <ul class="space-y-2">
                                    @foreach ($featureUsageStats['most_used'] as $f)
                                        <li class="flex justify-between text-sm">
                                            <span class="text-gray-800">{{ $f->feature_name }}@if($f->feature_category) <span class="text-gray-500">({{ $f->feature_category }})</span>@endif</span>
                                            <span class="font-medium text-gray-700">{{ number_format($f->count) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500">No feature usage yet.</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Least used features</p>
                            @if ($featureUsageStats['least_used']->isNotEmpty())
                                <ul class="space-y-2">
                                    @foreach ($featureUsageStats['least_used'] as $f)
                                        <li class="flex justify-between text-sm">
                                            <span class="text-gray-800">{{ $f->feature_name }}@if($f->feature_category) <span class="text-gray-500">({{ $f->feature_category }})</span>@endif</span>
                                            <span class="font-medium text-gray-700">{{ number_format($f->count) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500">No feature usage yet.</p>
                            @endif
                        </div>
                    </div>
                    @if ($recentFeatureUsage->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">Recent feature usage</p>
                            <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-48 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Feature</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentFeatureUsage as $f)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-800">{{ $f->feature_name }}</td>
                                                <td class="px-3 py-2 text-gray-500">{{ $f->feature_category ?? '—' }}</td>
                                                <td class="px-3 py-2 text-gray-500">{{ $f->occurred_at?->diffForHumans() ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    <p class="text-sm text-gray-500 mt-4">Send usage with <code class="rounded bg-gray-100 px-1">POST /api/v1/feature-usage</code> (feature_name, optional feature_category).</p>
                </div>
            </div>

            {{-- Module health scoring --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Module health scoring</h3>
                    <p class="text-sm text-gray-500 mt-1">Scores per module from errors, speed, and drop-offs (0–100; higher is better).</p>
                </div>
                <div class="p-6">
                    @if ($moduleHealthScores->isNotEmpty() || count($latestScoresByModule) > 0)
                        <div class="space-y-4">
                            @foreach ($latestScoresByModule as $m)
                                @php
                                    $label = $m->score >= 80 ? 'Healthy' : ($m->score >= 60 ? 'Fair' : ($m->score >= 40 ? 'Degraded' : 'Critical'));
                                    $bg = $m->score >= 80 ? 'bg-green-100 text-green-800' : ($m->score >= 60 ? 'bg-amber-100 text-amber-800' : ($m->score >= 40 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'));
                                @endphp
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $m->module_name ?: $m->module_id }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $m->module_id }}</p>
                                        @if ($m->errors_count !== null || $m->errors_score !== null || $m->speed_score !== null || $m->drop_off_score !== null)
                                            <p class="text-xs text-gray-500 mt-1">
                                                @if ($m->errors_count !== null) Errors: {{ $m->errors_count }} @endif
                                                @if ($m->errors_score !== null) · Errors score: {{ number_format($m->errors_score, 1) }} @endif
                                                @if ($m->speed_score !== null) · Speed: {{ number_format($m->speed_score, 1) }} @endif
                                                @if ($m->drop_off_score !== null) · Drop-off: {{ number_format($m->drop_off_score, 1) }} @endif
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $bg }}">{{ number_format($m->score, 1) }}</span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $label }} · {{ $m->recorded_at?->diffForHumans() ?? '—' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($moduleHealthScores->isNotEmpty())
                            <p class="text-sm text-gray-500 mt-4">Recent scores (last 30). Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/module-health</code> (module_id, score, optional errors_count, errors_score, speed_score, drop_off_score).</p>
                        @endif
                    @else
                        <p class="text-sm text-gray-500">No module health scores yet. Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/module-health</code> (module_id, score 0–100, optional errors_count, errors_score, speed_score, drop_off_score, period_type).</p>
                    @endif
                </div>
            </div>

            {{-- Security event monitoring --}}
            @if(!isset($platformSupports) || $platformSupports['security_events'] ?? true)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Security event monitoring</h3>
                    <p class="text-sm text-gray-500 mt-1">Failed logins, suspicious activity, and token abuse.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total events</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($securityEventStats['total']) }}</p>
                        </div>
                        @foreach (\App\Models\SecurityEvent::TYPES as $type => $label)
                            @php $count = $securityEventStats['by_type'][$type] ?? 0; @endphp
                            @if ($count > 0)
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                    <p class="mt-1 text-xl font-semibold {{ $type === 'failed_login' ? 'text-amber-600' : ($type === 'token_abuse' ? 'text-red-600' : 'text-orange-600') }}">{{ number_format($count) }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if ($recentSecurityEvents->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-72 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">User / IP</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentSecurityEvents as $e)
                                        <tr>
                                            <td class="px-3 py-2">
                                                @if ($e->event_type === 'failed_login')
                                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Failed login</span>
                                                @elseif ($e->event_type === 'token_abuse')
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Token abuse</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">Suspicious</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-gray-700">{{ $e->user_identifier ? Str::limit($e->user_identifier, 24) : '—' }} @if($e->ip_address)<span class="text-gray-500 font-mono text-xs">{{ $e->ip_address }}</span>@endif</td>
                                            <td class="px-3 py-2 text-gray-600">{{ Str::limit($e->reason ?? '—', 40) }}</td>
                                            <td class="px-3 py-2 text-gray-500">{{ $e->occurred_at?->diffForHumans() ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No security events yet. Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/security-events</code> (event_type: failed_login, suspicious_activity, token_abuse; optional ip_address, user_identifier, reason).</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Data integrity monitoring --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Data integrity monitoring</h3>
                    <p class="text-sm text-gray-500 mt-1">Detect negative stock, duplicate orders, and missing transactions.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total issues</p>
                            <p class="mt-1 text-xl font-semibold {{ $dataIntegrityStats['total'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ number_format($dataIntegrityStats['total']) }}</p>
                        </div>
                        @foreach (\App\Models\DataIntegrityEvent::TYPES as $type => $label)
                            @php $count = $dataIntegrityStats['by_type'][$type] ?? 0; @endphp
                            @if ($count > 0)
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                    <p class="mt-1 text-xl font-semibold text-amber-600">{{ number_format($count) }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if ($recentDataIntegrityEvents->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-72 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentDataIntegrityEvents as $e)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">{{ \App\Models\DataIntegrityEvent::TYPES[$e->event_type] ?? $e->event_type }}</span>
                                            </td>
                                            <td class="px-3 py-2 font-mono text-gray-700">{{ Str::limit($e->reference_id ?? '—', 20) }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ Str::limit($e->description ?? '—', 50) }}</td>
                                            <td class="px-3 py-2 text-gray-500">{{ $e->occurred_at?->diffForHumans() ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No data integrity events yet. Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/data-integrity</code> (event_type: negative_stock, duplicate_order, missing_transaction; optional reference_id, description).</p>
                    @endif
                </div>
            </div>

            {{-- Smart alert system --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Smart alert system</h3>
                    <p class="text-sm text-gray-500 mt-1">Alerts for revenue risk, error spikes, and performance drops.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total alerts</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($alertStats['total']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Unacknowledged</p>
                            <p class="mt-1 text-xl font-semibold {{ $alertStats['unacknowledged'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($alertStats['unacknowledged']) }}</p>
                        </div>
                        @foreach (\App\Models\Alert::TYPES as $type => $label)
                            @php $count = $alertStats['by_type'][$type] ?? 0; @endphp
                            @if ($count > 0)
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $label }}</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($count) }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if ($recentAlerts->isNotEmpty())
                        <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-72 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title / Message</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentAlerts as $a)
                                        <tr class="{{ $a->acknowledged_at ? 'bg-gray-50' : '' }}">
                                            <td class="px-3 py-2">
                                                @if ($a->alert_type === 'revenue_risk')
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Revenue risk</span>
                                                @elseif ($a->alert_type === 'error_spike')
                                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Error spike</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">Performance drop</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-gray-800">{{ Str::limit($a->title ?? $a->message ?? '—', 50) }}</td>
                                            <td class="px-3 py-2">
                                                @if ($a->severity === 'critical')
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Critical</span>
                                                @elseif ($a->severity === 'high')
                                                    <span class="inline-flex rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">High</span>
                                                @elseif ($a->severity === 'low')
                                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">Low</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Medium</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-gray-500">{{ $a->occurred_at?->diffForHumans() ?? '—' }} @if($a->acknowledged_at)<span class="text-gray-400 text-xs">· Acked</span>@endif</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No alerts yet. Send with <code class="rounded bg-gray-100 px-1">POST /api/v1/alerts</code> (alert_type: revenue_risk, error_spike, performance_drop; optional title, message, severity).</p>
                    @endif
                </div>
            </div>

            {{-- Funnels & User journeys (Phase 2) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">Funnels & user journeys</h3>
                        <p class="text-sm text-gray-500 mt-1">Funnel analytics (e.g. Inventory → Requests → Payment → Success) and full session journey visualization.</p>
                    </div>
                    <a href="{{ route('apps.funnels.index', $app) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Manage funnels</a>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Funnel & drop-off analytics</p>
                            <p class="text-sm text-gray-500 mb-3">Define multi-step funnels and track conversion and drop-off per step.</p>
                            <a href="{{ route('apps.funnels.index', $app) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View funnels →</a>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">User journey visualization</p>
                            <p class="text-sm text-gray-500 mb-2">Full navigation flow per session (screens, navigations, actions, journey steps).</p>
                            @if ($journeySessions->isNotEmpty())
                                <ul class="space-y-1 max-h-40 overflow-y-auto">
                                    @foreach ($journeySessions as $sid)
                                        <li>
                                            <a href="{{ route('apps.journey.show', [$app, $sid]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-mono truncate block" title="{{ $sid }}">View journey: {{ Str::limit($sid, 28) }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500">No sessions with tracking yet. Start sessions and send events with the same <code class="rounded bg-gray-100 px-1">session_id</code>.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- API keys --}}
            <div id="section-api-keys" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-6">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">API keys</h3>
                    <form action="{{ route('apps.api-keys.store', $app) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="name" placeholder="Key name (e.g. Production)" required class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" maxlength="255"/>
                        <x-primary-button type="submit">Generate key</x-primary-button>
                    </form>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse ($app->apiKeys as $apiKey)
                        <li class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $apiKey->name }}</p>
                                <p class="text-sm text-gray-500 font-mono">{{ $apiKey->key_prefix }}••••••••</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Last used: {{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : 'Never' }}
                                    @if ($apiKey->expires_at) · Expires {{ $apiKey->expires_at->format('M j, Y') }} @endif
                                </p>
                            </div>
                            <form action="{{ route('apps.api-keys.revoke', [$app, $apiKey]) }}" method="POST" onsubmit="return confirm('Revoke this API key? It will stop working immediately.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Revoke</button>
                            </form>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-center text-gray-500">
                            No API keys yet. Generate one above to send tracking data from your app.
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- SDK Instructions --}}
            @if(isset($sdkInstructions))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">SDK Setup Instructions</h3>
                                @if(isset($platformModel) && $platformModel)
                                    <p class="text-sm text-gray-500 mt-1">Platform: <span class="font-medium">{{ $platformModel->name }}</span> · {{ $platformModel->category }}</p>
                                @endif
                            </div>
                            @if(isset($platformModel) && $platformModel && $platformModel->default_features)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($platformModel->default_features as $feature)
                                        <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded">{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        @if(isset($sdkInstructions['install']))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Installation</h4>
                                <p class="text-sm text-gray-600">{{ $sdkInstructions['install'] }}</p>
                            </div>
                        @endif

                        @if(isset($sdkInstructions['config']))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Configuration</h4>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    @foreach($sdkInstructions['config'] as $key => $value)
                                        <div class="flex items-center gap-2">
                                            <code class="text-xs font-mono text-gray-700">{{ $key }}:</code>
                                            <code class="text-xs font-mono text-indigo-600">{{ $value }}</code>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(isset($sdkInstructions['code']))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Example Code</h4>
                                <div class="relative">
                                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>{{ $sdkInstructions['code'] }}</code></pre>
                                    <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-white text-xs rounded-md">Copy</button>
                                </div>
                            </div>
                        @endif

                        @if(isset($sdkInstructions['example']))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Example Request</h4>
                                <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>{{ $sdkInstructions['example'] }}</code></pre>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500">
                                <strong>Note:</strong> Make sure to set the <code class="rounded bg-gray-100 px-1">environment</code> field in your tracking calls (production, development, staging, testing).
                                This helps filter data in the dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tracking API info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-2">Generic API Reference</h3>
                <p class="text-sm text-gray-500 mb-4">Send events from your application using the API key in the <code class="rounded bg-gray-100 px-1">X-Api-Key</code> header or <code class="rounded bg-gray-100 px-1">Authorization: Bearer &lt;key&gt;</code>.</p>
                <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/track') }}
Content-Type: application/json
X-Api-Key: &lt;your-api-key&gt;

{
  "type": "page_view",
  "payload": { "path": "/dashboard", "title": "Dashboard" },
  "session_id": "optional-session-id",
  "user_id": "optional-user-id",
  "environment": "production"
}</code></pre>
                <p class="mt-4 text-sm text-gray-500">Batch: <code class="rounded bg-gray-100 px-1">POST {{ url('/api/v1/track/batch') }}</code> with <code class="rounded bg-gray-100 px-1">{"events": [...]}</code></p>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Session tracking</p>
                    <p class="text-sm text-gray-500 mb-2">Start, end, and heartbeat sessions; send cumulative foreground_seconds and background_seconds for mobile (e.g. app in background).</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/sessions/start') }}
{ "session_id": "uuid-or-unique-id", "user_id": "optional", "metadata": {} }

POST {{ url('/api/v1/sessions/heartbeat') }}
{ "session_id": "...", "foreground_seconds": 120, "background_seconds": 30 }

POST {{ url('/api/v1/sessions/end') }}
{ "session_id": "...", "duration_seconds": 150, "foreground_seconds": 120, "background_seconds": 30 }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Navigation tracking</p>
                    <p class="text-sm text-gray-500 mb-2">Record from_screen → to_screen with navigation_type: push, replace, or back.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/navigation') }}
{ "from_screen": "Home", "to_screen": "Profile", "navigation_type": "push", "session_id": "optional", "user_id": "optional" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Screen view tracking</p>
                    <p class="text-sm text-gray-500 mb-2">Record screen name, previous screen, timestamp, and load_time_ms.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/screen-views') }}
{ "screen_name": "Profile", "previous_screen": "Home", "load_time_ms": 120, "session_id": "optional", "user_id": "optional" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">User action tracking</p>
                    <p class="text-sm text-gray-500 mb-2">Record button clicks, dashboard access, payments, form submissions. action_type: button_click, dashboard_access, payment, form_submission, custom.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/user-actions') }}
{ "action_type": "button_click", "action_name": "submit_checkout", "target": "btn-submit", "payload": {}, "session_id": "optional", "user_id": "optional" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Error tracking</p>
                    <p class="text-sm text-gray-500 mb-2">Record errors with message, stack_trace, file, line, user_info, device_info, severity (debug|info|warning|error|critical).</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/errors') }}
{ "message": "Something went wrong", "stack_trace": "...", "file": "app.js", "line": 42, "severity": "error", "user_info": {}, "device_info": {} }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Performance monitoring</p>
                    <p class="text-sm text-gray-500 mb-2">Record screen load time, API response time, session duration. Set is_slow=true when value exceeds your threshold. Optional geo: country_code, country, region, city for geo-performance breakdown.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/performance-metrics') }}
{ "metric_type": "api_response", "name": "GET /api/users", "value": 250, "value_unit": "ms", "is_slow": false, "threshold": 500, "country_code": "US", "country": "United States", "region": "California", "city": "San Francisco" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Funnel steps</p>
                    <p class="text-sm text-gray-500 mb-2">Record when a user completes a funnel step. Use funnel_id (from dashboard) or funnel_slug.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/funnel-steps') }}
{ "funnel_id": 1, "step_key": "inventory", "session_id": "sess_123", "user_id": "optional" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">User journey (custom steps)</p>
                    <p class="text-sm text-gray-500 mb-2">Record custom journey steps for full navigation flow visualization. step_type: screen, action, custom.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/journey') }}
{ "step_name": "Viewed product", "step_type": "action", "payload": {}, "session_id": "sess_123", "user_id": "optional" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Business event tracking</p>
                    <p class="text-sm text-gray-500 mb-2">Track orders created, payments completed, inventory updates, product requests. event_type: order_created, payment_completed, inventory_update, product_request, custom.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/business-events') }}
{ "event_type": "order_created", "reference_id": "ord_123", "payload": { "amount": 99.99, "items": 2 }, "session_id": "optional", "user_id": "optional" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Financial impact (revenue impact)</p>
                    <p class="text-sm text-gray-500 mb-2">Record revenue impact of failed payments, system errors, downtime. impact_type: failed_payment, system_error, downtime, custom. amount in currency (default USD).</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/financial-impacts') }}
{ "impact_type": "failed_payment", "amount": 99.99, "currency": "USD", "reference_id": "ord_123", "description": "optional" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Offline & network monitoring</p>
                    <p class="text-sm text-gray-500 mb-2">Offline sessions (offline_start, offline_end with duration_seconds), sync_retry (retry_count, success), network_strength (weak/moderate/strong or value).</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/network-monitoring') }}
{ "event_type": "offline_end", "duration_seconds": 120, "session_id": "sess_123" }
{ "event_type": "sync_retry", "retry_count": 2, "success": true }
{ "event_type": "network_strength", "network_strength": "weak" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Third-Party API monitoring</p>
                    <p class="text-sm text-gray-500 mb-2">Payment, SMS, and email provider calls. provider_type: payment, sms, email. Include success, response_time_ms, status_code, optional provider_name, operation, error_message.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/third-party-api') }}
{ "provider_type": "payment", "provider_name": "Stripe", "operation": "charge", "success": true, "response_time_ms": 320, "status_code": 200 }
{ "provider_type": "sms", "provider_name": "Twilio", "success": false, "status_code": 503, "error_message": "Service unavailable" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Feature usage analytics</p>
                    <p class="text-sm text-gray-500 mb-2">Track which features are used most/least. feature_name required; optional feature_category, session_id, user_id.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/feature-usage') }}
{ "feature_name": "checkout", "feature_category": "commerce", "session_id": "sess_123" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Module health scoring</p>
                    <p class="text-sm text-gray-500 mb-2">Report module health (0–100). Optional: errors_count, errors_score, speed_score, drop_off_score, period_type (daily/weekly), period_start, period_end.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/module-health') }}
{ "module_id": "checkout", "module_name": "Checkout flow", "score": 85, "errors_count": 2, "errors_score": 90, "speed_score": 88, "drop_off_score": 82 }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Security event monitoring</p>
                    <p class="text-sm text-gray-500 mb-2">Track failed logins, suspicious activity, token abuse. event_type: failed_login, suspicious_activity, token_abuse. Optional: ip_address, user_identifier, reason, payload.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/security-events') }}
{ "event_type": "failed_login", "user_identifier": "user@example.com", "ip_address": "192.168.1.1", "reason": "Invalid password" }
{ "event_type": "token_abuse", "reason": "Expired token reuse attempt", "session_id": "sess_123" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Data integrity monitoring</p>
                    <p class="text-sm text-gray-500 mb-2">Report negative stock, duplicate orders, missing transactions. event_type: negative_stock, duplicate_order, missing_transaction. Optional: reference_id, description, payload.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/data-integrity') }}
{ "event_type": "negative_stock", "reference_id": "SKU-123", "description": "Stock went below zero after order" }
{ "event_type": "duplicate_order", "reference_id": "ord_456", "description": "Duplicate order id detected" }</code></pre>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="font-medium text-gray-900 mb-2">Smart alert system</p>
                    <p class="text-sm text-gray-500 mb-2">Send alerts for revenue risk, error spikes, performance drops. alert_type: revenue_risk, error_spike, performance_drop. Optional: title, message, severity (low|medium|high|critical), payload, channel.</p>
                    <pre class="rounded-lg bg-gray-900 text-gray-100 p-4 text-sm overflow-x-auto"><code>POST {{ url('/api/v1/alerts') }}
{ "alert_type": "revenue_risk", "title": "Payment failure spike", "message": "Failed payments up 40% in last hour", "severity": "high" }
{ "alert_type": "performance_drop", "title": "API latency", "message": "P95 &gt; 2s", "severity": "critical", "payload": { "p95_ms": 2100 } }</code></pre>
                </div>
            </div>

            {{-- Recent events --}}
            <div id="section-recent-events" class="scroll-mt-6">
                @if ($recentEvents->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Recent events</h3>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach ($recentEvents as $event)
                            <li class="px-6 py-3 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $event->type }}</span>
                                <span class="text-gray-500">{{ $event->created_at->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900">Recent events</h3>
                    <p class="text-sm text-gray-500 mt-1">No events in selected period. Send events via the tracking API.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    <style>html { scroll-behavior: smooth; }</style>
    <script>
        document.getElementById('date_preset_app')?.addEventListener('change', function() {
            document.getElementById('custom_dates_app')?.classList.toggle('hidden', this.value !== 'custom');
        });

        function copyCode(button) {
            const codeBlock = button.previousElementSibling;
            const text = codeBlock.textContent;
            navigator.clipboard.writeText(text).then(() => {
                button.textContent = 'Copied!';
                setTimeout(() => {
                    button.textContent = 'Copy';
                }, 2000);
            });
        }
    </script>
</x-app-layout>
