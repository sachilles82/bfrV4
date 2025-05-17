@props(['statusFilter', 'options'])

<div
    class="order-last flex w-full gap-x-8 text-sm/6 font-semibold sm:order-none sm:w-auto sm:border-l sm:border-gray-200 sm:dark:border-gray-700 sm:pl-6 sm:text-sm/7">

    @foreach($options as $status)
        {{-- Beispiel für Active - passe die Logik für die anderen ggf. an oder mache es dynamischer --}}
        @if($status['value'] === 'active')
            <a href="#"
               wire:click.prevent="$set('statusFilter', '{{ $status['value'] }}')"
               class="{{ $statusFilter === $status['value'] ? 'dark:text-indigo-400 text-indigo-600' : 'hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400 text-gray-700' }}">

                {{ __('Active') }}

            </a>
        @elseif($status['value'] === 'archived')
            <a href="#"
               wire:click.prevent="$set('statusFilter', '{{ $status['value'] }}')"
               class="{{ $statusFilter === $status['value'] ? 'dark:text-indigo-400 text-indigo-600' : 'hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400 text-gray-700' }}">

                {{ __('Archived') }}

            </a>
        @elseif($status['value'] === 'trashed')
            <a href="#"
               wire:click.prevent="$set('statusFilter', '{{ $status['value'] }}')"
               class="{{ $statusFilter === $status['value'] ? 'dark:text-indigo-400 text-indigo-600' : 'hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400 text-gray-700' }}">

                {{ __('In Trash') }}

            </a>
        @endif

    @endforeach

</div>
