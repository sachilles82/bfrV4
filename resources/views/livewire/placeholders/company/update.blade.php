<x-pupi.layout.form-skeleton>
    <x-slot:title>
        {{ __('Company Information') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Here are stored the company information') }}
    </x-slot:description>

    <x-slot name="form">
        {{-- Static skeleton (no wire:submit etc.) --}}
        <div class="px-4 mt-2 py-6 sm:p-8 animate-pulse">
            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                <!-- Company Name (col-span-4) -->
                <div class="sm:col-span-4">
                    <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

                <!-- Company Type (col-span-2) -->
                <div class="sm:col-span-2">
                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

                <!-- Industry (col-span-4) -->
                <div class="sm:col-span-4">
                    <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

                <!-- Company Size (col-span-2) -->
                <div class="sm:col-span-2">
                    <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

                <!-- UID Number (col-span-3) -->
                <div class="sm:col-span-3">
                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

                <!-- Company URL (col-span-4) -->
                <div class="sm:col-span-4">
                    <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <!-- “URL + suffix” Skeleton: two blocks side by side -->
                    <div class="mt-2 flex w-full">
                        <div class="h-10 flex-1 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                        <div class="h-10 w-32 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    </div>
                </div>

                <!-- Telefon 1 (col-span-3) -->
                <div class="sm:col-span-3">
                    <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

                <!-- Telefon 2 (col-span-3) -->
                <div class="sm:col-span-3">
                    <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

                <!-- Company Email (col-span-4) -->
                <div class="sm:col-span-4">
                    <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

                <!-- Owner Name (col-span-4) -->
                <div class="sm:col-span-4">
                    <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="mt-2 h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                </div>

            </div>
        </div>

        {{-- Skeleton button container (same spacing as your real button container) --}}
        <x-pupi.button.container>
            <div class="h-10 w-32 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
        </x-pupi.button.container>
    </x-slot>
</x-pupi.layout.form-skeleton>
