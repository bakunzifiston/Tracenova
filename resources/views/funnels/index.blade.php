<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('apps.show', $app) }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Funnels — {{ $app->name }}</h2>
            </div>
            <a href="{{ route('apps.funnels.create', $app) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Add funnel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
            @endif

            @if ($funnels->isEmpty())
                <div class="bg-white rounded-lg shadow p-12 text-center text-gray-500">
                    <p class="mb-4">No funnels yet. Create a funnel to track conversion steps (e.g. Inventory → Requests → Payment → Success).</p>
                    <a href="{{ route('apps.funnels.create', $app) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Create your first funnel →</a>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <ul class="divide-y divide-gray-200">
                        @foreach ($funnels as $funnel)
                            <li class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                                <div>
                                    <a href="{{ route('apps.funnels.show', [$app, $funnel]) }}" class="font-medium text-gray-900">{{ $funnel->name }}</a>
                                    <p class="text-sm text-gray-500 mt-1">{{ implode(' → ', $funnel->steps) }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm text-gray-500">{{ number_format($funnel->step_events_count) }} step events</span>
                                    <a href="{{ route('apps.funnels.show', [$app, $funnel]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View analytics</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
