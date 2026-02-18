<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('apps.show', $app) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit app') }} — {{ $app->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('apps.update', $app) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" value="App name"/>
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $app->name)" required autofocus/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div>
                        <x-input-label for="slug" value="Slug"/>
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $app->slug)"/>
                        <x-input-error :messages="$errors->get('slug')" class="mt-2"/>
                    </div>

                    <div>
                        <x-input-label for="platform_id" value="Platform"/>
                        <select id="platform_id" name="platform_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Select Platform --</option>
                            @foreach($platforms as $category => $categoryPlatforms)
                                <optgroup label="{{ $category }}">
                                    @foreach($categoryPlatforms as $platform)
                                        <option value="{{ $platform->id }}" {{ old('platform_id', $app->platform_id) == $platform->id ? 'selected' : '' }}>
                                            {{ $platform->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Select a platform to get platform-specific SDK instructions.</p>
                        <x-input-error :messages="$errors->get('platform_id')" class="mt-2"/>
                    </div>

                    <div>
                        <x-input-label for="description" value="Description (optional)"/>
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $app->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="is_tracking_enabled" value="0"/>
                        <input id="is_tracking_enabled" name="is_tracking_enabled" type="checkbox" value="1" {{ old('is_tracking_enabled', $app->is_tracking_enabled) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"/>
                        <x-input-label for="is_tracking_enabled" value="Enable tracking for this app" class="ml-2"/>
                    </div>

                    <div class="flex gap-3">
                        <x-primary-button type="submit">{{ __('Update app') }}</x-primary-button>
                        <a href="{{ route('apps.show', $app) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
