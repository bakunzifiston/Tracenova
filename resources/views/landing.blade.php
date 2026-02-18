<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="TraceNova — Enterprise application monitoring, tracking & business intelligence. Error tracking, performance monitoring, user journeys, and revenue impact in one platform.">
    <title>{{ config('app.name', 'TraceNova') }} — Monitoring & Intelligence Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="antialiased text-slate-900 bg-white">
    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <span class="flex w-9 h-9 rounded-lg bg-indigo-600 items-center justify-center text-white font-bold text-lg">T</span>
                <span class="font-semibold text-slate-900">TraceNova</span>
            </a>
            <nav class="flex items-center gap-6">
                <a href="#features" class="text-sm font-medium text-slate-600 hover:text-slate-900">Features</a>
                <a href="#how-it-works" class="text-sm font-medium text-slate-600 hover:text-slate-900">How it works</a>
                <a href="#use-cases" class="text-sm font-medium text-slate-600 hover:text-slate-900">Use cases</a>
                @auth
                    <a href="{{ route('apps.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Dashboard</a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Log in</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Get Started</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="pt-16">
        {{-- Hero --}}
        <section class="relative overflow-hidden bg-gradient-to-b from-slate-50 to-white border-b border-slate-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
                <div class="text-center max-w-3xl mx-auto">
                    <div class="inline-flex w-14 h-14 rounded-xl bg-indigo-600 items-center justify-center text-white font-bold text-2xl mb-6">T</div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 tracking-tight">Application Monitoring &amp; Intelligence</h1>
                    <p class="mt-4 text-xl text-slate-600">One platform to track errors, performance, user journeys, and business impact — from web, mobile, and any app.</p>
                    <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/25 hover:bg-indigo-700">Get Started</a>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 rounded-lg border-2 border-slate-300 text-slate-700 font-semibold hover:border-slate-400 hover:bg-slate-50">View Dashboard Demo</a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="py-20 sm:py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-slate-900 text-center">Core capabilities</h2>
                <p class="mt-3 text-slate-600 text-center max-w-2xl mx-auto">Everything you need to understand system health, user behavior, and business impact.</p>
                <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach([
                        ['title' => 'Error Tracking', 'desc' => 'Capture errors with stack traces, severity, and context. Debug faster with file, line, and device info.', 'icon' => 'exclamation'],
                        ['title' => 'Performance Monitoring', 'desc' => 'Screen load time, API response time, session duration. Flag slow performance and track by geo.', 'icon' => 'chart'],
                        ['title' => 'User Journey Analytics', 'desc' => 'Full navigation flow per session. Screens, actions, and custom steps in one timeline.', 'icon' => 'path'],
                        ['title' => 'Security Monitoring', 'desc' => 'Failed logins, suspicious activity, token abuse. IP and user context for every event.', 'icon' => 'shield'],
                        ['title' => 'Business Intelligence Tracking', 'desc' => 'Orders, payments, inventory updates, product requests. Event types and reference IDs.', 'icon' => 'briefcase'],
                        ['title' => 'Financial Impact Insights', 'desc' => 'Revenue impact of failed payments, system errors, and downtime. Amount and currency per type.', 'icon' => 'currency'],
                    ] as $f)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-6 hover:border-indigo-200 hover:bg-indigo-50/30 transition-colors">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4">
                                @if($f['icon'] === 'exclamation')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @elseif($f['icon'] === 'chart')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                @elseif($f['icon'] === 'path')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                @elseif($f['icon'] === 'shield')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @elseif($f['icon'] === 'briefcase')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <h3 class="font-semibold text-slate-900">{{ $f['title'] }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $f['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- System Monitoring Preview --}}
        <section class="py-20 sm:py-24 bg-slate-50 border-y border-slate-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-slate-900 text-center">System monitoring at a glance</h2>
                <p class="mt-3 text-slate-600 text-center max-w-2xl mx-auto">Real-time dashboard: sessions, errors, performance, and business events.</p>
                <div class="mt-12 rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                    <div class="h-10 bg-slate-100 border-b border-slate-200 flex items-center gap-2 px-4">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-green-400"></span>
                        <span class="ml-4 text-sm text-slate-500">Dashboard — TraceNova</span>
                    </div>
                    <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach(['Sessions', 'Errors', 'Performance', 'Events'] as $label)
                            <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
                                <p class="text-xs font-medium text-slate-500 uppercase">{{ $label }}</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $label === 'Sessions' ? '1,247' : ($label === 'Errors' ? '12' : ($label === 'Performance' ? '98%' : '3,891')) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 pb-6 flex gap-4">
                        <div class="flex-1 h-48 rounded-lg bg-slate-100 border border-slate-200 flex items-end gap-1 p-4">
                            @foreach([40, 65, 45, 80, 55, 70, 90, 60, 75, 85] as $h)
                                <span class="flex-1 rounded-t bg-indigo-500" style="height: {{ $h }}%"></span>
                            @endforeach
                        </div>
                        <div class="flex-1 h-48 rounded-lg bg-slate-100 border border-slate-200 p-4">
                            <p class="text-xs text-slate-500">Recent activity</p>
                            <ul class="mt-2 space-y-2 text-sm text-slate-700">
                                <li>Session started — user_abc</li>
                                <li>Screen: Checkout</li>
                                <li>Payment completed</li>
                                <li>Error: Network timeout</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Advanced Features --}}
        <section class="py-20 sm:py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-slate-900 text-center">Beyond basic monitoring</h2>
                <p class="mt-3 text-slate-600 text-center max-w-2xl mx-auto">Features that give you an edge: revenue impact, funnels, module health, and API dependency tracking.</p>
                <ul class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 list-none">
                    @foreach(['Revenue impact monitoring', 'Role-based analytics', 'API dependency monitoring', 'Funnel analytics', 'Module health scoring'] as $item)
                        <li class="flex items-start gap-4 rounded-xl border border-slate-200 p-5 hover:border-indigo-200 hover:bg-slate-50/50 transition-colors">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="font-medium text-slate-900">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- How It Works --}}
        <section id="how-it-works" class="py-20 sm:py-24 bg-slate-50 border-y border-slate-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-slate-900 text-center">How it works</h2>
                <p class="mt-3 text-slate-600 text-center max-w-2xl mx-auto">From your app to insights in four steps.</p>
                <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach([
                        ['step' => '1', 'title' => 'Your App', 'desc' => 'Web, React Native, WordPress, or any platform sending events.'],
                        ['step' => '2', 'title' => 'Tracking SDK', 'desc' => 'Lightweight client that captures sessions, errors, and custom events.'],
                        ['step' => '3', 'title' => 'TraceNova API', 'desc' => 'Secure ingestion with API keys. REST endpoints for every event type.'],
                        ['step' => '4', 'title' => 'Dashboard Analytics', 'desc' => 'Real-time views, funnels, journeys, and business impact.'],
                    ] as $s)
                        <div class="relative rounded-xl border border-slate-200 bg-white p-6 text-center">
                            <span class="inline-flex w-10 h-10 rounded-full bg-indigo-600 text-white font-bold items-center justify-center">{{ $s['step'] }}</span>
                            <h3 class="mt-4 font-semibold text-slate-900">{{ $s['title'] }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $s['desc'] }}</p>
                            @if(!$loop->last)
                                <div class="hidden lg:block absolute top-1/2 -right-3 w-6 h-0.5 bg-slate-300"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Use Cases --}}
        <section id="use-cases" class="py-20 sm:py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-slate-900 text-center">Built for your industry</h2>
                <p class="mt-3 text-slate-600 text-center max-w-2xl mx-auto">From e-commerce to government platforms, TraceNova scales with your stack.</p>
                <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach([
                        ['E-commerce', 'Checkout funnels, payment errors, and revenue impact.'],
                        ['AgriTech', 'Session and usage analytics for field and farm apps.'],
                        ['Inventory Systems', 'Data integrity, duplicate orders, and stock events.'],
                        ['Government Platforms', 'Security events, compliance, and audit trails.'],
                        ['FinTech Apps', 'Performance, failed payments, and financial impact.'],
                    ] as $u)
                        <div class="rounded-xl border border-slate-200 p-5 hover:border-indigo-200 hover:shadow-md transition-all">
                            <h3 class="font-semibold text-slate-900">{{ $u[0] }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $u[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Testimonials (placeholder) --}}
        <section class="py-20 sm:py-24 bg-slate-50 border-y border-slate-200">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <blockquote class="text-xl text-slate-700 font-medium">“We needed one place to see errors, performance, and business impact. TraceNova gave us exactly that — and the funnel analytics changed how we optimize checkout.”</blockquote>
                <p class="mt-6 text-slate-500">— Product Lead, E-commerce Platform</p>
            </div>
        </section>

        {{-- CTA Banner --}}
        <section class="py-20 sm:py-24 bg-indigo-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-white">Ready to see your app in a new light?</h2>
                <p class="mt-4 text-indigo-100">Create an account and connect your first app in minutes.</p>
                <div class="mt-10">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-indigo-600 font-semibold hover:bg-indigo-50">Get Started — It’s free</a>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="bg-slate-900 text-slate-300 py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div class="flex items-center gap-2">
                        <span class="flex w-8 h-8 rounded-lg bg-indigo-500 items-center justify-center text-white font-bold">T</span>
                        <span class="font-semibold text-white">TraceNova</span>
                    </div>
                    <nav class="flex flex-wrap gap-6 text-sm">
                        <a href="{{ url('/#features') }}" class="hover:text-white">Docs</a>
                        <a href="{{ route('login') }}" class="hover:text-white">API reference</a>
                        <a href="mailto:contact@example.com" class="hover:text-white">Contact</a>
                        <a href="{{ url('/') }}" class="hover:text-white">Company</a>
                    </nav>
                </div>
                <p class="mt-8 text-sm text-slate-500">© {{ date('Y') }} TraceNova. Application monitoring &amp; business intelligence.</p>
            </div>
        </footer>
    </main>
</body>
</html>
