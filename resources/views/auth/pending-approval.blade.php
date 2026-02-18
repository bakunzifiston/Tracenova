<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Your account has been created and is pending approval by an administrator.') }}
    </div>
    @if (session('message'))
        <p class="mb-4 text-gray-700">{{ session('message') }}</p>
    @endif
    <p class="mb-6 text-gray-500 text-sm">
        {{ __('You will be able to create and manage your monitoring projects once an administrator approves your account. You can log out or wait on this page.') }}
    </p>
    <div class="flex items-center justify-between">
        <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900">{{ __('Back to login') }}</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <x-primary-button type="submit">{{ __('Log Out') }}</x-primary-button>
        </form>
    </div>
</x-guest-layout>
