<div {{ $attributes->merge(['class' => "flex h-full min-w-0 flex-1 flex-col lg:order-last overflow-x-hidden"]) }}>
    <div class="flex-1 xl:flex">
        <div class="px-4 py-4 sm:px-6 lg:pl-8 xl:flex-1 xl:pl-6">

            <div class="relative">
                <div
                    class="flex flex-col items-center justify-between py-0 md:py-0 space-y-3 md:flex-row md:space-y-0 md:space-x-4">

                    <div class="w-full md:w-1/2">
                        <div class="flex items-center">
                            <div>
                                 Titel
                                @isset($title)
                                    <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
                                        {{ $title }}
                                    </h1>
                                @endisset

                                 Untertitel
                                @isset($description)
                                    <p class="mt-0 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $description }}
                                    </p>
                                @endisset
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-end shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center">

                        @isset($create)
                            {{ $create }}
                        @endisset

                    </div>

                </div>
            </div>

            {{-- Hier wird die Tabelle gerendert --}}
            {{ $slot }}

        </div>
    </div>
</div>
