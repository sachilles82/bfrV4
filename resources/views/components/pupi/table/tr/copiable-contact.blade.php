<!-- resources/views/components/pupi/table/tr/copiable-contact.blade.php -->
@props(['value', 'link' => null, 'linkText' => null, 'linkPrefix' => '', 'fallbackText' => null, 'id' => null])

@php
    $uniqueId = $id ?? 'copiable-' . md5($value . time() . rand(1000, 9999));
    $linkValue = $link ?? ($linkPrefix ? $linkPrefix . $value : null);
    $displayText = $linkText ?? $value;
@endphp

<div class="flex items-center group"
     x-data="{
        justCopied: false,
        message: '',
        copyText(text) {
            navigator.clipboard.writeText(text);
            this.justCopied = true;
            this.message = 'Kopiert!';
            setTimeout(() => {
                this.justCopied = false;
                this.message = '';
            }, 3000);
        }
     }">
    @if($value)
        @if($linkValue)
            <a href="{{ $linkValue }}"
               class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 mr-2 decoration-1 hover:underline">
                {{ $displayText }}
            </a>
        @else
            <span class="text-gray-900 dark:text-gray-300 mr-2">
                {{ $displayText }}
            </span>
        @endif
        <button
            @click.prevent="copyText('{{ $value }}')"
            class="opacity-0 group-hover:opacity-100 inline-flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-opacity duration-200"
            title="Kopieren">
            <template x-if="!justCopied">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/>
                    <path
                        d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"/>
                </svg>
            </template>
            <template x-if="justCopied">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5 text-green-500 dark:text-green-400" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
            </template>
        </button>
    @elseif($fallbackText)
        <span class="italic text-gray-400 dark:text-gray-500 mr-2">
            {{ $fallbackText }}
        </span>
    @endif
</div>
