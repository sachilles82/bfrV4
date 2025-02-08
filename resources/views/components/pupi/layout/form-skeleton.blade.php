<div class="grid max-w-8xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">

    <div class="px-4 sm:px-0">
        <h2 class="text-base/7 font-semibold dark:text-white text-gray-900">

            {{$title}}

        </h2>
        <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">

            {{ $description }}

        </p>
    </div>

    <div class="animate-pulse bg-gray-50 dark:bg-white/5 shadow-sm ring-1 ring-gray-700/5 sm:rounded-md rounded-lg md:col-span-2">

        {{$form}}

    </div>

</div>
