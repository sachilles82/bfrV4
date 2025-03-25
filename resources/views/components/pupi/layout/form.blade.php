<div class="grid max-w-8xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">

    <div class="px-4 sm:px-0">
        <h2 class="text-base/7 font-semibold dark:text-white text-gray-900">
            {{$title}}
        </h2>
        <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">
            {{ $description }}
        </p>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-xs ring-1 ring-gray-900/5 sm:rounded-md md:col-span-2 relative">
        <div wire:loading class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-gray-900/50 backdrop-blur-xs rounded-lg"></div>
        {{$form}}
    </div>

</div>
