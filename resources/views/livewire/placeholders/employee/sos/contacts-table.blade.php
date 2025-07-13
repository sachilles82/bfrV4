<div>
    <x-pupi.layout.form>
        <x-slot:title>
            {{ __('SOS Emergency Contact') }}
        </x-slot:title>

        <x-slot:description>
            {{ __('Update the emergency contact data') }}
        </x-slot:description>

        <x-slot name="form">
            <x-pupi.table2.container>
                <x-slot:table>
                    <x-pupi.table2.main>
                        <x-slot:head>
                            <x-pupi.table2.th.th>
                                <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                            </x-pupi.table2.th.th>
                            <x-pupi.table2.th.notsort>
                                <div class="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                <div class="h-4 w-12 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.actions/>
                        </x-slot:head>

                        <x-slot:body>
                            {{-- Skeleton Row 1 --}}
                            <x-pupi.table2.tr.body>
                                <x-pupi.table2.tr.cell1>
                                    <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell1>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.action>
                                    <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.action>
                            </x-pupi.table2.tr.body>

                            {{-- Skeleton Row 2 --}}
                            <x-pupi.table2.tr.body>
                                <x-pupi.table2.tr.cell1>
                                    <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell1>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.action>
                                    <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.action>
                            </x-pupi.table2.tr.body>

                            {{-- Skeleton Row 3 --}}
                            <x-pupi.table2.tr.body>
                                <x-pupi.table2.tr.cell1>
                                    <div class="h-4 w-36 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell1>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.cell>
                                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.cell>
                                <x-pupi.table2.tr.action>
                                    <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                </x-pupi.table2.tr.action>
                            </x-pupi.table2.tr.body>
                        </x-slot:body>
                    </x-pupi.table2.main>
                </x-slot:table>
            </x-pupi.table2.container>

            <!-- Button Container Skeleton -->
            <x-pupi.button.container>
                <div class="h-10 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            </x-pupi.button.container>

        </x-slot>
    </x-pupi.layout.form>
</div>
