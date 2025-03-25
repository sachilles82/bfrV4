<div>
    <div
        class="xl:mt-2 mt-1 h-full flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4">
        <div class="w-full lg:w-1/3 md:w-1/2">
            <x-pupi.actions.search wire:model.live.debounce.400ms="search"/>
        </div>
        <div
            class="flex flex-col items-stretch justify-end shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
            <div class="flex items-center justify-end w-full space-x-1 md:w-auto">
                <x-pupi.actions.reset-filters wire:click="resetFilters"/>
                <x-pupi.actions.per-page/>
            </div>
        </div>
    </div>

    <x-pupi.table.container>
        <x-pupi.table.main>
            <x-slot:head>
                <x-pupi.table.th.th>
                    {{ __('Name') }}
                </x-pupi.table.th.th>
                <x-pupi.table.th.notsort>
                    {{ __('Access Portal') }}
                </x-pupi.table.th.notsort>
                <x-pupi.table.th.notsort>
                    {{ __('Total Permissions') }}
                </x-pupi.table.th.notsort>
                <x-pupi.table.th.notsort>
                    {{ __('Created By') }}
                </x-pupi.table.th.notsort>
                <x-pupi.table.th.actions/>
            </x-slot:head>
            <x-slot:body>
                @forelse($roles as $role)
                    <tr
                        wire:key="{{ $role->id }}"
                        class="hover:bg-gray-100 dark:hover:bg-gray-800/50"
                    >
                        <x-pupi.table.tr.tr>
                            <a wire:navigate.hover
                               href="{{ route('settings.roles.show', [$role->id, 'baseApp']) }}"
                               class="inline-flex items-center rounded-lg px-2 py-1 text-sm font-medium ring-1 ring-inset mr-2
                                           bg-indigo-50 text-indigo-800 ring-indigo-700/10 hover:text-indigo-600 hover:bg-indigo-100 hover:ring-indigo-600/10 decoration-1 hover:underline
                                           dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-indigo-400/30
                                           dark:hover:text-indigo-300 dark:hover:bg-indigo-400/20">
                                <x-pupi.icon.shield-check class="w-5 h-5 mr-2"/>
                                {{$role->name}}
                            </a>
                        </x-pupi.table.tr.tr>
                        <x-pupi.table.tr.cell>
                            <div
                                class="text-sm leading-6 text-gray-900 dark:text-white">{{ $role->access->label() }}</div>
                            <div
                                class="text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $role->description }}</div>
                        </x-pupi.table.tr.cell>
                        <x-pupi.table.tr.cell>
                               <span
                                   class="cursor-default inline-flex items-center rounded-lg px-2 py-1 text-sm font-medium ring-1 ring-inset mr-2
                                       bg-yellow-50 text-yellow-800 ring-yellow-600/20
                                       dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20
                                       ">
                                   <x-pupi.icon.key class="w-5 h-5 mr-2"/>
                                       {{ $role->permissions_count }}
                                   </span>
                        </x-pupi.table.tr.cell>
                        <x-pupi.table.tr.cell>

                            <flux:tooltip content="{{ optional($role->creator)->name }}">
                                <div>
                                    <flux:button variant="ghost"><img
                                            src="https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                            alt="" class="size-6 flex-none rounded-full bg-gray-800"></flux:button>
                                </div>
                            </flux:tooltip>
                        </x-pupi.table.tr.cell>
                        <flux:cell>
{{--                            @can('update', $role)--}}
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal" size="sm"
                                                 variant="ghost" inset="top bottom"/>

                                    <flux:menu class="min-w-32">
                                        <flux:menu.item wire:click="showEditModal({{ $role->id }})"
                                                        icon="pencil-square">
                                            {{ __('Edit') }}
                                        </flux:menu.item>

                                        <flux:menu.item wire:click="delete({{ $role->id }})"
                                                        wire:confirm="{{ __('Are you sure you want to remove this role?') }}"
                                                        wire:confirm.prompt="Are you sure?\n\nType YES to confirm|YES"
                                                        icon="trash" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
{{--                            @endcan--}}
                        </flux:cell>
                    </tr>
                @empty
                    <x-pupi.table.tr.empty>
                        <x-pupi.table.tr.empty-cell colspan="6"/>
                    </x-pupi.table.tr.empty>
                @endforelse
            </x-slot:body>
            <x-slot:pagination>
                {{ $roles->links() }}
            </x-slot:pagination>
        </x-pupi.table.main>
    </x-pupi.table.container>


    <flux:modal name="role-edit" variant="flyout" class="space-y-6">
        <form wire:submit="update" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Edit Role') }}</flux:heading>
                <flux:subheading>{{ __('Edit this role') }}</flux:subheading>
            </div>
            <div class="pt-4 pb-8">
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Role Name')}}"
                                            for="name"
                                            :error="$errors->first('name')"
                                            help-text="{{ __('') }}">
                            <x-pupi.input.text autofocus wire:model.live="name"
                                               name="name" id="name"
                                               placeholder="{{ __('Role')}}"/>
                        </x-pupi.input.group>
                    </div>
                    <div class="sm:col-span-3">
                        <flux:select
                            wire:model="access"
                            label="{{ __('Access Panel') }}"
                            id="access"
                            name="access"
                            variant="listbox"
                        >
                            @foreach($accessOptions as $option)
                                <flux:option value="{{ $option->value }}">
                                    {{ $option->label() }}
                                </flux:option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="col-span-full">
                        <x-pupi.input.group label="{{ __('Description')}}"
                                            for="description"
                                            :error="$errors->first('description')"
                                            help-text="{{ __('*max 60 Letter! Describe the Role you will create') }}">
                            <x-pupi.input.textarea rows="2" autofocus wire:model="description"
                                                   name="description" id="description"
                                                   placeholder="{{ __('Role with Full Access')}}"/>
                        </x-pupi.input.group>
                    </div>
                </div>
            </div>


            <div class="flex">
                <flux:spacer/>
                <flux:button type="submit" variant="primary">{{ __('Update') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
