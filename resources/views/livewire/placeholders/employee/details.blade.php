<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Account Details') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employee account details.') }}
    </x-slot:description>

    <x-slot name="form">
        <div class="animate-pulse">
            <form>
                <div class="px-4 py-6 sm:p-8">
                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                        <!-- Avatar Section Skeleton -->
                        <div class="col-span-full flex items-center gap-x-8">
                            <div class="h-24 w-24 flex-none rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                            <div>
                                <div class="h-9 w-32 rounded-md bg-gray-200 dark:bg-gray-700 mb-2"></div>
                                <div class="h-4 w-40 rounded bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Gender Field Skeleton (col-span-4) -->
                        <div class="sm:col-span-4">
                            <div class="space-y-2">
                                <div class="h-4 w-16 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- First Name Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-5">
                            <div class="space-y-2">
                                <div class="h-4 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Email Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-16 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Phone Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-16 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Teams Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-16 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Department Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-10 w-full rounded-md bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                        </div>

                        <!-- Department Field Skeleton (col-span-3) -->
                        <div class="sm:col-span-3">
                            <div class="space-y-2">
                                <div class="h-4 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
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
