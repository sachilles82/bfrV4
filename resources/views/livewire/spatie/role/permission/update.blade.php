<x-pupi.layout.form>
    <x-slot name="title">
        <div class="inline-flex items-center rounded-lg px-2 py-1 text-sm font-medium ring-1 ring-inset mr-2
            bg-indigo-50 text-indigo-800 ring-indigo-700/10 hover:text-indigo-600 hover:bg-indigo-100 hover:ring-indigo-600/10 decoration-1 hover:underline
            dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-indigo-400/30
            dark:hover:text-indigo-300 dark:hover:bg-indigo-400/20">
            <x-pupi.icon.shield-check class="w-5 h-5 mr-2"/>
            {{$role->name}}</div>
    </x-slot>

    <x-slot name="description">
        {{$role->description}}
    </x-slot>

    <x-slot name="form">

        <form wire:submit.prevent="updateRole"
              class="bg-white dark:bg-gray-900 shadow-xs ring-1 ring-gray-900/5 sm:rounded-lg md:col-span-2">

            <div class="flex items-center gap-x-4 p-6 pb-1">
                <div class="flex items-center gap-x-6">
                    <img src="{{ $appData['logo'] }}"
                         alt="Logo"
                         class="size-14 flex-none rounded-lg bg-white object-cover ring-1 ring-gray-900/10">

                    <h1>
                        <div class="mt-1 text-base font-semibold leading-6 dark:text-white text-gray-900">
                            {{ $appData['title'] }}
                        </div>
                        <div class="text-sm leading-6 dark:text-gray-400 text-gray-500">
                            App <span class="dark:text-gray-500 text-gray-700">{{ $appData['version'] }}</span>
                        </div>
                    </h1>
                </div>
            </div>
            <div class="relative">
                <div class="p-2 mt-2 border-t border-gray-100 dark:border-white/10">

                    @can('update', $role)
                        <div
                            class="relative divide-y divide-gray-200 overflow-hidden sm:grid sm:grid-cols-2 sm:gap-px sm:divide-y-0">

                            <div class="group relative dark:bg-gray-900 bg-white p-4">
                                <div class="flex w-full items-center justify-between space-x-6">
                                    <div class="flex-1 truncate">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-base font-semibold dark:text-white leading-6 text-gray-900">
                                                {{ __('Full Access') }}
                                            </h3>
                                            <flux:tooltip content="{{ __('Check this box to give full access to this role') }}">
                                            <span
                                                class="inline-flex shrink-0 items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset cursor-help bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20">
                                                {{ __('Info') }}
                                            </span>
                                            </flux:tooltip>
                                        </div>
                                        <div class="absolute right-4 top-6 h-6 w-4 dark:yellow-400/20 text-yellow-600">
                                            <div x-data="checkAll"
                                                 x-on:update-table.window="$refs.checkbox.checked = false; $refs.checkbox.indeterminate = false;"
                                                 class="flex rounded-md shadow-xs">
                                                <input x-ref="checkbox" @change="handleCheck" type="checkbox"
                                                       class="absolute -mt-1 h-4 w-4 rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-500 dark:checked:border-indigo-500 dark:focus:ring-offset-gray-800">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endcan

                    <!-- Permissions List Bereich -->
                    <div
                        class="relative divide-y divide-gray-200 overflow-hidden sm:grid sm:grid-cols-2 sm:gap-px sm:divide-y-0">
                        <!-- Für jede Gruppe -->
                        @foreach($permissionsByGroup as $group => $permissions)
                            <div class="group relative dark:bg-gray-900 bg-white p-4">
                                <div>
                                    <div class="mt-4">
                                        <fieldset>
                                            <legend
                                                class="text-base font-semibold dark:text-white leading-6 text-gray-900 mb-4">
                                                {{ ucfirst($group) }} {{ __('Permissions') }}
                                            </legend>
                                            <div
                                                class="divide-y divide-gray-200 dark:divide-white/10 border-t dark:border-white/10 border-gray-200">

                                                    <flux:accordion transition exclusive class="pt-4">
                                                        @foreach($permissions as $permission)
                                                        <flux:accordion.item icon="disabled">
                                                            <flux:accordion.heading>
                                                                <div class="relative flex items-start">
                                                                    <div for="permission-{{ $permission->value }}"
                                                                         class="min-w-0 flex-1 text-sm/6">
                                                                        <label class="font-medium text-gray-900 dark:text-gray-400">
                                                                            {{ $permission->value }}
                                                                        </label>
                                                                    </div>
                                                                    <div class="ml-3 flex h-6 items-center">
                                                                        @can('update', $role)
                                                                            <x-pupi.input.checkbox
                                                                                type="checkbox"
                                                                                wire:model="selectedIds"
                                                                                id="permission-{{ $permission->value }}"
                                                                                value="{{ $permission->value }}"
                                                                            />
                                                                        @else
                                                                            <x-pupi.input.checkbox
                                                                                type="checkbox"
                                                                                wire:model="selectedIds"
                                                                                id="permission-{{ $permission->value }}"
                                                                                value="{{ $permission->value }}"
                                                                                disabled
                                                                            />
                                                                        @endcan
                                                                    </div>

                                                                </div>
                                                            </flux:accordion.heading>

                                                            <flux:accordion.content>
                                                                <p>{{ $permission->description() }}</p>
                                                            </flux:accordion.content>
                                                        </flux:accordion.item>
                                                        @endforeach
                                                    </flux:accordion>


                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div wire:loading wire:target="store"
                     class="absolute inset-0 dark:bg-gray-900 bg-gray-50 opacity-65 rounded-lg"></div>
                <div wire:loading.flex wire:target="store" class="absolute inset-0 flex justify-center items-center">
                    <x-pupi.icon.spinner class="text-gray-500 dark:text-indigo-500"/>
                </div>

            </div>
            @can('update', $role)
                <x-pupi.button.container>
                    <x-pupi.button.fluxback/>
                    <x-pupi.button.fluxsubmit
                    />
                </x-pupi.button.container>
            @endcan
        </form>
    </x-slot>

    @script
    <script>
        Alpine.data('checkAll', () => ({
            init() {
                this.updateCheckAllState();
                this.$wire.$watch('selectedIds', () => {
                    this.updateCheckAllState()
                })
                this.$wire.$watch('idsOnPage', () => {
                    this.updateCheckAllState()
                })
            },
            updateCheckAllState() {
                if (this.pageIsSelected()) {
                    this.$refs.checkbox.checked = true
                    this.$refs.checkbox.indeterminate = false
                } else if (this.pageIsEmpty()) {
                    this.$refs.checkbox.checked = false
                    this.$refs.checkbox.indeterminate = false
                } else {
                    this.$refs.checkbox.checked = false
                    this.$refs.checkbox.indeterminate = true
                }
            },
            pageIsSelected() {
                return this.$wire.idsOnPage.every(id => this.$wire.selectedIds.includes(id));
            },
            pageIsEmpty() {
                return !this.$wire.idsOnPage.some(id => this.$wire.selectedIds.includes(id));
            },
            handleCheck(e) {
                e.target.checked ? this.selectAllItems() : this.deselectAll();
                this.$dispatch('check-all', e.target.checked);
            },
            selectAllItems() {
                this.$wire.idsOnPage.forEach(id => {
                    if (!this.$wire.selectedIds.includes(id)) {
                        this.$wire.selectedIds.push(id);
                    }
                });
            },
            deselectAll() {
                this.$wire.selectedIds = this.$wire.selectedIds.filter(id => {
                    return !this.$wire.idsOnPage.includes(id);
                });
            },

        }));
    </script>
    @endscript

</x-pupi.layout.form>
