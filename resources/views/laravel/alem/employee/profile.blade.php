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

                <a href="#" aria-label="Latest on our blog">
                    <flux:card size="sm" class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                        <flux:heading class="flex items-center gap-2">Profession Manager
                            <flux:icon name="arrow-up-right" class="ml-auto text-zinc-400" variant="micro" />
                        </flux:heading>
                        <flux:text class="mt-2">16 Profession in the Database are stored.</flux:text>
                        <flux:modal.trigger
                            name="create-stage"
                            @click="$dispatch('open-stage-manager')"
                        >
                            <x-pupi.button.open-manager/>
                        </flux:modal.trigger>
                    </flux:card>
                </a>
                <li class="col-span-1 flex rounded-md shadow-xs">
                    <div class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-pink-600 text-sm font-medium text-white">PR</div>
                    <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
                        <div class="flex-1 truncate px-4 py-2 text-sm">
                            <a href="#" class="font-medium text-gray-900 hover:text-gray-600">Profession Manager</a>
                            <p class="text-gray-500">16 Professions</p>
                        </div>
                        <div class="shrink-0 pr-2">
                            <button type="button" class="inline-flex size-8 items-center justify-center rounded-full bg-transparent bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                <span class="sr-only">Open options</span>
                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
                <li class="col-span-1 flex rounded-md shadow-xs">
                    <div class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-purple-600 text-sm font-medium text-white">CD</div>
                    <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
                        <div class="flex-1 truncate px-4 py-2 text-sm">
                            <a href="#" class="font-medium text-gray-900 hover:text-gray-600">Component Design</a>
                            <p class="text-gray-500">12 Members</p>
                        </div>
                        <div class="shrink-0 pr-2">
                            <button type="button" class="inline-flex size-8 items-center justify-center rounded-full bg-transparent bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                <span class="sr-only">Open options</span>
                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
                <li class="col-span-1 flex rounded-md shadow-xs">
                    <div class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-yellow-500 text-sm font-medium text-white">T</div>
                    <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
                        <div class="flex-1 truncate px-4 py-2 text-sm">
                            <a href="#" class="font-medium text-gray-900 hover:text-gray-600">Templates</a>
                            <p class="text-gray-500">16 Members</p>
                        </div>
                        <div class="shrink-0 pr-2">
                            <button type="button" class="inline-flex size-8 items-center justify-center rounded-full bg-transparent bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                <span class="sr-only">Open options</span>
                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
                <li class="col-span-1 flex rounded-md shadow-xs">
                    <div class="flex w-16 shrink-0 items-center justify-center rounded-l-md bg-green-500 dark:bg-green-400/10 text-sm font-medium text-white">RC</div>
                    <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 dark:border-white/10 dark:dark:bg-gray-800 bg-white">
                        <div class="flex-1 truncate px-4 py-2 text-sm">
                            <a href="#" class="font-medium text-gray-900 dark:text-white hover:text-gray-600">React Components</a>
                            <p class="text-gray-500 dark:text-gray-400">8 Members</p>
                        </div>
                        <div class="shrink-0 pr-2">
                            <button type="button" class="inline-flex size-8 items-center justify-center rounded-full bg-transparent bg-white dark:dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                <span class="sr-only">Open options</span>
                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>


    </x-pupi.layout.container>

</x-app-layout>
