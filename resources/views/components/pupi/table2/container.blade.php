<div>
    {{$table}}
    {{-- Pagination --}}
    @if(isset($pagination))
        <div class="border-t border-gray-200 dark:border-gray-700/50 px-4 py-1 sm:px-6">
            {{ $pagination }}
        </div>
    @endif

</div>
