<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('apps.funnels.index', $app) }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $funnel->name }}</h2>
                    <p class="text-sm text-gray-500">{{ implode(' → ', $funnel->steps) }}</p>
                </div>
            </div>
            <a href="{{ route('apps.funnels.edit', [$app, $funnel]) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Edit</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
            @endif

            {{-- Funnel visualization: bar / funnel --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Conversion funnel</h3>
                @php
                    $steps = $funnel->steps ?? [];
                    $maxCount = count($stepCounts) > 0 ? max(array_values($stepCounts)) : 1;
                @endphp
                <div class="space-y-4">
                    @foreach ($steps as $index => $stepKey)
                        @php
                            $count = $stepCounts[$index] ?? 0;
                            $pct = $maxCount > 0 ? round(100 * $count / $maxCount) : 0;
                            $dropOff = $dropOffs[$index] ?? null;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $stepKey }}</span>
                                <span class="text-gray-500">{{ number_format($count) }} sessions
                                    @if ($dropOff !== null && $dropOff > 0)
                                        <span class="text-amber-600">(−{{ $dropOff }} drop-off)</span>
                                    @endif
                                </span>
                            </div>
                            <div class="h-8 bg-gray-100 rounded overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded flex items-center justify-end pr-2" style="width: {{ max($pct, 2) }}%">
                                    @if ($pct >= 15)
                                        <span class="text-xs font-medium text-white">{{ $pct }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Step counts table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Step breakdown</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Step</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sessions reached</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Drop-off to next</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($funnel->steps ?? [] as $index => $stepKey)
                                <tr>
                                    <td class="px-6 py-3 text-gray-900">{{ $stepKey }}</td>
                                    <td class="px-6 py-3 text-gray-700">{{ number_format($stepCounts[$index] ?? 0) }}</td>
                                    <td class="px-6 py-3">
                                        @if (isset($dropOffs[$index]) && $dropOffs[$index] > 0)
                                            <span class="text-amber-600 font-medium">−{{ number_format($dropOffs[$index]) }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-600">
                <strong>API:</strong> Send step events with <code class="rounded bg-gray-200 px-1">POST /api/v1/funnel-steps</code> using <code class="rounded bg-gray-200 px-1">funnel_id: {{ $funnel->id }}</code> or <code class="rounded bg-gray-200 px-1">funnel_slug: "{{ $funnel->slug }}"</code> and <code class="rounded bg-gray-200 px-1">step_key</code> (e.g. "{{ $funnel->steps[0] ?? 'inventory' }}"), <code class="rounded bg-gray-200 px-1">session_id</code>, <code class="rounded bg-gray-200 px-1">user_id</code>.
            </div>
        </div>
    </div>
</x-app-layout>
