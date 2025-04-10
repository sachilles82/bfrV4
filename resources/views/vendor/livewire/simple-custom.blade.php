@php
    if (!isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
        : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            {{-- Previous Page Link --}}
            <div>
                @if ($paginator->onFirstPage())
                    <span class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-400 focus:outline-hidden disabled cursor-not-allowed dark:text-gray-500">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    @if (method_exists($paginator, 'getCursorName'))
                        <button type="button" dusk="previousPage" wire:key="cursor-{{ $paginator->getCursorName() }}-{{ $paginator->previousCursor()->encode() }}" wire:click="setPage('{{$paginator->previousCursor()->encode()}}','{{ $paginator->getCursorName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/5 dark:focus:bg-white/5">
                            {!! __('pagination.previous') !!}
                        </button>
                    @else
                        <button
                            type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/5 dark:focus:bg-white/5">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                @endif
            </div>

{{--            --}}{{-- Results Information --}}
{{--            <div class="text-center mx-auto">--}}
{{--                <p class="text-sm text-gray-700 dark:text-gray-400 leading-5">--}}
{{--                    <span class="font-medium">{{ $paginator->firstItem() }}</span>--}}
{{--                    <span>{!! __('to') !!}</span>--}}
{{--                    <span class="font-medium">{{ $paginator->lastItem() }}</span>--}}
{{--                    <span>{!! __('of') !!}</span>--}}
{{--                    <span class="font-medium">{{ $paginator->total() }}</span>--}}
{{--                    <span>{!! __('results') !!}</span>--}}
{{--                </p>--}}
{{--            </div>--}}

            {{-- Next Page Link --}}
            <div>
                @if ($paginator->hasMorePages())
                    @if (method_exists($paginator, 'getCursorName'))
                        <button type="button" dusk="nextPage" wire:key="cursor-{{ $paginator->getCursorName() }}-{{ $paginator->nextCursor()->encode() }}" wire:click="setPage('{{$paginator->nextCursor()->encode()}}','{{ $paginator->getCursorName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/5 dark:focus:bg-white/5">
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/5 dark:focus:bg-white/5">
                            {!! __('pagination.next') !!}
                        </button>
                    @endif
                @else
                    <span class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-400 focus:outline-hidden disabled cursor-not-allowed dark:text-gray-500">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
