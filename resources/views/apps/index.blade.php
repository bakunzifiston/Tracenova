<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Apps') }}
            </h2>
            <a href="{{ route('apps.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Add app') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($apps->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <p class="mb-4">No apps yet. Register your first app to start receiving tracking data.</p>
                        <a href="{{ route('apps.create') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            Create your first app →
                        </a>
                    </div>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($apps as $app)
                            <li class="px-6 py-4 hover:bg-gray-50">
                                <a href="{{ route('apps.show', $app) }}" class="block">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 font-semibold">
                                                {{ strtoupper(substr($app->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $app->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $app->slug }} · @if($app->platform_id && $app->relationLoaded('platform') && $app->getRelation('platform')){{ $app->getRelation('platform')->name }}@else{{ \App\Models\App::PLATFORMS[$app->platform] ?? $app->platform ?? '—' }}@endif</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-6">
                                            <span class="text-sm text-gray-500">
                                                {{ $app->api_keys_count }} key(s) · {{ number_format($app->tracking_events_count) }} events
                                            </span>
                                            @if ($app->is_tracking_enabled)
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Tracking on</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Tracking off</span>
                                            @endif
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="px-6 py-3 border-t border-gray-200">
                        {{ $apps->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
