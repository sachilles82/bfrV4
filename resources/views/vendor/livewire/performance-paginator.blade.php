@php
    // $paginator und $hasMorePages sind jetzt verfügbar
    if (!isset($scrollTo)) {
        $scrollTo = 'body';
    }
    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
        : '';
@endphp

<div>
    @if ($paginator->onFirstPage() && !$hasMorePages)
        {{-- Zeige nichts an, wenn wir auf Seite 1 sind UND es keine weiteren Seiten gibt --}}
    @else
        {{-- Zeige die Navigation an, wenn wir NICHT auf Seite 1 sind ODER wenn es weitere Seiten gibt --}}
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            {{-- Previous Page Link --}}
            <div>
                @if ($paginator->onFirstPage())
                    <span class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-400 focus:outline-hidden disabled cursor-not-allowed dark:text-gray-500">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    {{-- Logik für Previous-Button (unverändert) --}}
                    @if (method_exists($paginator, 'getCursorName') && $paginator->previousCursor())
                        <button type="button" dusk="previousPage" wire:key="cursor-{{ $paginator->getCursorName() }}-{{ $paginator->previousCursor()->encode() }}" wire:click="setPage('{{$paginator->previousCursor()->encode()}}','{{ $paginator->getCursorName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/5 dark:focus:bg-white/5">
                            {!! __('pagination.previous') !!}
                        </button>
                    @elseif (method_exists($paginator, 'previousPageUrl'))
                        <button
                            type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/5 dark:focus:bg-white/5">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                @endif
            </div>

            {{-- Next Page Link --}}
            <div>
                {{-- HIER DIE ÄNDERUNG: Prüfe die übergebene $hasMorePages Variable --}}
                @if ($hasMorePages)
                    {{-- Logik für Next-Button (unverändert) --}}
                    @if (method_exists($paginator, 'getCursorName') && $paginator->nextCursor())
                        <button type="button" dusk="nextPage" wire:key="cursor-{{ $paginator->getCursorName() }}-{{ $paginator->nextCursor()->encode() }}" wire:click="setPage('{{$paginator->nextCursor()->encode()}}','{{ $paginator->getCursorName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/5 dark:focus:bg-white/5">
                            {!! __('pagination.next') !!}
                        </button>
                    @elseif (method_exists($paginator, 'nextPageUrl'))
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
