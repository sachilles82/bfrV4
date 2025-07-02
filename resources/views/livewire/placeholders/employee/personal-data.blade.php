<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Employee Employment Data') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employment data') }}
    </x-slot:description>

    <x-slot name="form">
        <div class="animate-pulse">
            <form>
                <div class="px-4 py-6 sm:p-8">
                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                        <!-- AHV Number Field Skeleton (col-span-4) -->
                        <div class="sm:col-span-4">
                            <div class="space-y-2">
                                <div class="h-4 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Nationality Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Hometown Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Birthdate Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-18 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Religion Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-16 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Civil Status Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-22 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Residence Permit Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-28 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Button Container Skeleton -->
                <div
                    class="flex items-center justify-end gap-x-2 border-t border-gray-900/10 dark:border-white/5 px-4 py-4 sm:px-8">
                    <div class="flex justify-end">
                        <div class="h-10 w-24 rounded-md bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                </div>
            </form>
        </div>
    </x-slot>
</x-pupi.layout.form>
