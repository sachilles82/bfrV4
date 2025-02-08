<!-- resources/views/errors/403.blade.php -->
<x-app-layout>
    <div class="mx-auto max-w-xl py-12">
        <h1 class="text-4xl font-bold mb-4">{{ __('404 - Page not found!') }}</h1>
        <p>{{ __('Something wrong was happens.') }}</p>
        <a wire:navigate.hover href="{{ url()->previous() }}" class="text-blue-500 underline">{{ __('Go back') }}</a>
    </div>
</x-app-layout>
