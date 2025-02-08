<!-- resources/views/errors/403.blade.php -->
<x-app-layout>
    <div class="mx-auto max-w-xl py-12">
        <h1 class="text-4xl font-bold mb-4">{{ __('503 - Server crashed!') }}</h1>
        <p>{{ __('No Panic, i will fix it!!') }}</p>
        <a wire:navigate.hover href="{{ url()->previous() }}" class="text-blue-500 underline">{{ __('Go back') }}</a>
    </div>
</x-app-layout>
