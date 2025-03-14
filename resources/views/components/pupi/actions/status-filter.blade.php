@props(['statusFilter'])

@php
    $statuses = \App\Enums\Model\ModelStatus::cases();
@endphp
<div
    class="order-last flex w-full gap-x-8 text-sm/6 font-semibold sm:order-none sm:w-auto sm:border-l sm:border-gray-200 sm:dark:border-gray-500 sm:pl-6 sm:text-sm/7">
    <a href="#"
       wire:click.prevent="$set('statusFilter', 'active')"
       class="{{ $statusFilter === 'active' ? 'dark:text-indigo-400 text-indigo-600' : 'hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400 text-gray-700' }}">
        {{ __('Active') }}
    </a>
    <a href="#"
       wire:click.prevent="$set('statusFilter', 'archived')"
       class="{{ $statusFilter === 'archived' ? 'dark:text-indigo-400 text-indigo-600' : 'hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400 text-gray-700' }}">
        {{ __('Archived') }}
    </a>
    <a href="#"
       wire:click.prevent="$set('statusFilter', 'trashed')"
       class="{{ $statusFilter === 'trashed' ? 'dark:text-indigo-400 text-indigo-600' : 'hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400 text-gray-700' }}">
        {{ __('Trashed') }}
    </a>
</div>

