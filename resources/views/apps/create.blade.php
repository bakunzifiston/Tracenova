<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('apps.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Register new app') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('apps.store') }}" method="POST" id="app-form">
                @csrf

                <!-- Platform Selection Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose Your Platform</h3>
                        
                        <!-- Category Tabs -->
                        <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-4">
                            <a href="{{ route('apps.create') }}" 
                               class="px-4 py-2 rounded-md text-sm font-medium {{ !$selectedCategory ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                                All
                            </a>
                            @foreach(\App\Models\Platform::CATEGORIES as $catKey => $catLabel)
                                <a href="{{ route('apps.create', ['category' => $catKey]) }}" 
                                   class="px-4 py-2 rounded-md text-sm font-medium {{ $selectedCategory === $catKey ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                                    {{ $catLabel }}
                                </a>
                            @endforeach
                        </div>

                        <!-- Platform Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6" id="platform-grid">
                            @php
                                $displayPlatforms = $selectedCategory 
                                    ? ($platforms[$selectedCategory] ?? collect())
                                    : $platforms->flatten();
                            @endphp
                            @foreach($displayPlatforms as $platform)
                                <label class="platform-card relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:border-indigo-400 transition-colors {{ old('platform_id') == $platform->id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                    <input type="radio" name="platform_id" value="{{ $platform->id }}" 
                                           class="sr-only peer" 
                                           {{ old('platform_id') == $platform->id ? 'checked' : '' }}
                                           required>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">{{ $platform->name }}</div>
                                        @if($platform->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $platform->description }}</p>
                                        @endif
                                        @if($platform->default_features)
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                @foreach(array_slice($platform->default_features, 0, 3) as $feature)
                                                    <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-700 rounded">{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                                                @endforeach
                                                @if(count($platform->default_features) > 3)
                                                    <span class="text-xs text-gray-500">+{{ count($platform->default_features) - 3 }} more</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <svg class="w-5 h-5 text-indigo-600 ml-2 hidden peer-checked:block absolute top-2 right-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </label>
                            @endforeach
                        </div>
                        @error('platform_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- App Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                    <div>
                        <x-input-label for="name" value="App name"/>
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div>
                        <x-input-label for="slug" value="Slug (optional)"/>
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug')" placeholder="e.g. my-web-app"/>
                        <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from name. Only lowercase letters, numbers, hyphens, underscores.</p>
                        <x-input-error :messages="$errors->get('slug')" class="mt-2"/>
                    </div>

                    <div>
                        <x-input-label for="description" value="Description (optional)"/>
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="is_tracking_enabled" value="0"/>
                        <input id="is_tracking_enabled" name="is_tracking_enabled" type="checkbox" value="1" {{ old('is_tracking_enabled', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"/>
                        <x-input-label for="is_tracking_enabled" value="Enable tracking for this app" class="ml-2"/>
                    </div>

                    <div class="flex gap-3">
                        <x-primary-button type="submit" id="submit-btn" disabled>{{ __('Create app') }}</x-primary-button>
                        <a href="{{ route('apps.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Enable submit button when platform is selected
        document.querySelectorAll('input[name="platform_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('submit-btn').disabled = false;
            });
        });

        // Check if platform is already selected (from old input)
        if (document.querySelector('input[name="platform_id"]:checked')) {
            document.getElementById('submit-btn').disabled = false;
        }
    </script>
</x-app-layout>
