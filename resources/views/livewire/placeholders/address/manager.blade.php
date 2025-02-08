<x-pupi.layout.form>

    <x-slot:title>
        {{ __('Address') }}
    </x-slot:title>

    <x-slot:description>
        {{__('Here are stored the address information')}}
    </x-slot:description>

    <x-slot name="form">


        <div
            class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
            <div class="px-4 py-6 sm:p-8">

                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <div class="sm:col-span-6 md:col-span-5 flex">
                        <div class="mt-4">
                            <label class="block text-sm/6 font-medium text-gray-900">Country</label>
                            <div class="mt-2">
                                <div class="relative mt-2">
                                    <div role="status" class="space-y-2.5 animate-pulse max-w-lg">
                                        <div class="flex items-center w-full">
                                            <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Country Dropdown End-->

                        <div class="mt-4 relative w-full">
                            <label class="block text-sm/6 font-medium text-gray-900">State</label>
                            <div class="mt-2">
                                <div class="relative mt-2">
                                    <div role="status" class="space-y-2.5 animate-pulse max-w-lg">
                                        <div class="flex items-center w-full">
                                            <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-32"></div>
                                            <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-24"></div>
                                            <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm/6 font-medium text-gray-900">Street Number</label>
                        <div class="mt-2">
                            <div class="relative mt-2">
                                <div role="status" class="space-y-2.5 animate-pulse max-w-lg">
                                    <div class="flex items-center w-full">
                                        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-32"></div>
                                        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-24"></div>
                                        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm/6 font-medium text-gray-900">Zip City</label>
                        <div class="mt-2">
                            <div class="relative mt-2">
                                <div role="status" class="space-y-2.5 animate-pulse max-w-lg">
                                    <div class="flex items-center w-full">
                                        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-32"></div>
                                        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-24"></div>
                                        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Button Container with Action Buttons -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </div>

    </x-slot>

</x-pupi.layout.form>


{{--<div role="status" class="space-y-2.5 animate-pulse max-w-lg">--}}
{{--    <div class="flex items-center w-full">--}}
{{--        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-32"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-24"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>--}}
{{--    </div>--}}
{{--    <div class="flex items-center w-full max-w-[480px]">--}}
{{--        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-full"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-24"></div>--}}
{{--    </div>--}}
{{--    <div class="flex items-center w-full max-w-[400px]">--}}
{{--        <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-200 rounded-full dark:bg-gray-700 w-80"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>--}}
{{--    </div>--}}
{{--    <div class="flex items-center w-full max-w-[480px]">--}}
{{--        <div class="h-2.5 ms-2 bg-gray-200 rounded-full dark:bg-gray-700 w-full"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-24"></div>--}}
{{--    </div>--}}
{{--    <div class="flex items-center w-full max-w-[440px]">--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-32"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-24"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-200 rounded-full dark:bg-gray-700 w-full"></div>--}}
{{--    </div>--}}
{{--    <div class="flex items-center w-full max-w-[360px]">--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-200 rounded-full dark:bg-gray-700 w-80"></div>--}}
{{--        <div class="h-2.5 ms-2 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>--}}
{{--    </div>--}}
{{--    <span class="sr-only">Loading...</span>--}}
{{--</div>--}}

