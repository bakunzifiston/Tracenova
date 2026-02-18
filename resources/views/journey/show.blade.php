<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('apps.show', $app) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">User journey</h2>
                <p class="text-sm text-gray-500 font-mono">Session: {{ Str::limit($sessionId, 36) }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Navigation flow & events</h3>
                    <p class="text-sm text-gray-500 mt-1">Full timeline for this session (journey steps, screens, navigations, actions).</p>
                </div>
                @if (empty($items))
                    <div class="p-12 text-center text-gray-500">
                        No events recorded for this session yet. Send journey steps, screen views, navigation, or user actions with the same <code class="rounded bg-gray-100 px-1">session_id</code>.
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($items as $item)
                            @php
                                $typeColors = [
                                    'journey' => 'bg-blue-100 text-blue-800',
                                    'screen' => 'bg-green-100 text-green-800',
                                    'navigation' => 'bg-purple-100 text-purple-800',
                                    'action' => 'bg-amber-100 text-amber-800',
                                ];
                                $color = $typeColors[$item['type']] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <li class="px-6 py-4 flex gap-4">
                                <div class="shrink-0 w-24 text-sm text-gray-500">
                                    {{ $item['at']?->format('H:i:s') ?? '—' }}
                                </div>
                                <div class="shrink-0">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $color }}">{{ $item['type'] }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900">{{ $item['label'] }}</p>
                                    @if (!empty($item['detail']))
                                        <p class="text-sm text-gray-500">{{ $item['detail'] }}</p>
                                    @endif
                                    @if (!empty($item['payload']) && is_array($item['payload']))
                                        <p class="text-xs text-gray-400 mt-1">{{ json_encode($item['payload']) }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
