<x-app-layout>

    <x-pupi.layout.container>

        {{-- Sidebar-Slot nur einfügen, wenn du eine Sidebar auf diese Seite hast --}}
        <x-slot:sidebar>
            <x-navigation.settings.sidebar
            />
        </x-slot:sidebar>

        <!-- Header-Slot nur einfügen, wenn du einen Header auf diese Seite hast -->
        {{--        <x-slot:header>--}}
        {{--            <x-navigation.alem.employee.header--}}
        {{--            />--}}
        {{--        </x-slot:header>--}}

        {{--Hier werden die livewire Componenten gerendert--}}
        {{--        <livewire:setting.theme--}}
        {{--        />--}}
        <livewire:alem.quick-crud.stage.stage-form
        />

        <livewire:alem.quick-crud.profession.profession-form
        />

        <div class="max-w-8xl px-4 py-10 sm:px-6 lg:px-8">

            <h2 class="text-sm font-medium text-gray-500">Quick Cruds</h2>
            <ul role="list" class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
                <li class="col-span-1 flex rounded-md shadow-xs">
                    <div
                        class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-pink-600 text-sm font-medium text-white">
                        SM
                    </div>
                    <div
                        class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white cursor-default">
                        <div class="flex-1 truncate px-4 py-2 text-sm">
                            <p class="font-medium text-gray-900 ">Stage Manager</p>
                            <p class="text-gray-500">16 Stages</p>
                        </div>
                        <div class="shrink-0 pr-2">
                            <flux:modal.trigger
                                name="create-stage"
                                @click="$dispatch('open-stage-manager')"
                            >
                                <flux:button variant="subtle" icon="pencil-square"/>
                            </flux:modal.trigger>
                        </div>
                    </div>
                </li>
                <li class="col-span-1 flex rounded-md shadow-xs">
                    <div
                        class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-pink-600 text-sm font-medium text-white">
                        PM
                    </div>
                    <div
                        class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white cursor-default">
                        <div class="flex-1 truncate px-4 py-2 text-sm">
                            <p class="font-medium text-gray-900 ">Profession Manager</p>
                            <p class="text-gray-500">12 Professions</p>
                        </div>
                        <div class="shrink-0 pr-2">
                            <flux:modal.trigger
                                name="create-profession"
                                @click="$dispatch('open-profession-manager')"
                            >
                                <flux:button variant="subtle" icon="pencil-square"/>
                            </flux:modal.trigger>
                        </div>
                    </div>
                </li>

            </ul>
        </div>


    </x-pupi.layout.container>

</x-app-layout>
