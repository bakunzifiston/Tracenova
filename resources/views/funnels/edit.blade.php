<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('apps.funnels.show', [$app, $funnel]) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit funnel — {{ $funnel->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('apps.funnels.update', [$app, $funnel]) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-input-label for="name" value="Funnel name"/>
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $funnel->name)" required/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>
                    <div>
                        <x-input-label for="steps" value="Steps (in order)"/>
                        <textarea id="steps" name="steps" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('steps', implode("\n", $funnel->steps ?? [])) }}</textarea>
                        <x-input-error :messages="$errors->get('steps')" class="mt-2"/>
                    </div>
                    <div class="flex gap-3">
                        <x-primary-button type="submit">Update funnel</x-primary-button>
                        <a href="{{ route('apps.funnels.show', [$app, $funnel]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
