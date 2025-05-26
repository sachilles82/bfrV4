<div>
    <flux:modal.trigger name="create-stage">
        <x-pupi.button.open-manager/>
    </flux:modal.trigger>

    <flux:modal name="create-stage" variant="flyout" class="w-1/3">
        <!-- Modal-Header -->
        <div>
            <flux:heading size="lg">
                {{ __('Stage Manager') }}
            </flux:heading>
            <flux:subheading>
                {{ $editing ? __('Edit the stage details.') : __('Fill out the details to create a new stage.') }}
            </flux:subheading>
        </div>

        <!-- Formular: Create/Update Stage -->
        <form wire:submit.prevent="saveStage" class="space-y-4">
            <div class="sm:col-span-3">
                <x-pupi.input.group
                    label="{{ __('Stage') }}"
                    for="name"
                    badge="{{ __('Required') }}"
                    :error="$errors->first('name')">
                    <x-pupi.input.text wire:model.defer="name" id="name" placeholder="{{ __('Enter stage name') }}"/>
                </x-pupi.input.group>
            </div>

            <!-- Formular-Buttons -->
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button wire:click="resetForm" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">
                    {{ $editing ? __('Update') : __('Create') }}
                </flux:button>
            </div>
        </form>

        <div class="flex flex-col pt-6" wire:key="stages-table-{{ now() }}">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="border border-gray-200 rounded-lg shadow-xs overflow-hidden dark:border-neutral-700 dark:shadow-gray-900">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                    {{ __('Name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                    {{ __('Created By') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                    {{ __('Action') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse($stages as $stage)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{ $stage->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{ optional($stage->creator)->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                        <div class="flex justify-end items-center gap-2">
                                            <button
                                                wire:click="editStage({{ $stage->id }})"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-400"
                                            >
                                                {{ __('Edit') }}
                                            </button>
                                            <button
                                                wire:click="deleteStage({{ $stage->id }})"
                                                wire:confirm="{{ __('Are you sure you want to remove this stage?') }}"
                                                class="text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400"
                                            >
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('No entries found...') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4 px-4">
                {{ $stages->links() }}
            </div>
        </div>



{{--        <!-- Tabelle mit vorhandenen Stages -->--}}
{{--        <flux:table>--}}
{{--            <flux:columns>--}}
{{--                <flux:column class="text-sm! font-semibold">{{ __('Stage') }}</flux:column>--}}
{{--                <flux:column class="text-sm! font-semibold">{{ __('Actions') }}</flux:column>--}}
{{--            </flux:columns>--}}

{{--            <flux:rows>--}}
{{--                @forelse ($stages as $stage)--}}
{{--                    <flux:row :key="$stage->id" class="hover:bg-gray-100">--}}
{{--                        <flux:cell>--}}
{{--                            <span class="text-sm font-medium">{{ $stage->name }}</span>--}}
{{--                        </flux:cell>--}}
{{--                        <x-pupi.table.tr.cell>--}}

{{--                            <flux:tooltip content="{{ optional($stage->creator)->name }}">--}}
{{--                                <div>--}}
{{--                                    <flux:button variant="ghost"><img--}}
{{--                                            src="https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"--}}
{{--                                            alt="" class="size-6 flex-none rounded-full bg-gray-800"></flux:button>--}}
{{--                                </div>--}}
{{--                            </flux:tooltip>--}}
{{--                        </x-pupi.table.tr.cell>--}}
{{--                        <flux:cell>--}}
{{--                            <flux:dropdown align="end" offset="-15">--}}
{{--                                <flux:button--}}
{{--                                    class="hover:bg-gray-200/75"--}}
{{--                                    icon="ellipsis-horizontal"--}}
{{--                                    size="sm"--}}
{{--                                    variant="ghost"--}}
{{--                                    inset="top bottom"--}}
{{--                                />--}}
{{--                                <flux:menu class="min-w-32">--}}
{{--                                    <flux:menu.item--}}
{{--                                        wire:click="editStage({{ $stage->id }})"--}}
{{--                                        icon="pencil-square"--}}
{{--                                    >--}}
{{--                                        {{ __('Edit') }}--}}
{{--                                    </flux:menu.item>--}}
{{--                                    <flux:menu.item--}}
{{--                                        wire:click="deleteStage({{ $stage->id }})"--}}
{{--                                        wire:confirm="{{ __('Are you sure you want to remove this stage?') }}"--}}
{{--                                        icon="trash"--}}
{{--                                        variant="danger"--}}
{{--                                    >--}}
{{--                                        {{ __('Delete') }}--}}
{{--                                    </flux:menu.item>--}}
{{--                                </flux:menu>--}}
{{--                            </flux:dropdown>--}}
{{--                        </flux:cell>--}}
{{--                    </flux:row>--}}
{{--                @empty--}}
{{--                    <flux:row>--}}
{{--                        <flux:cell colspan="2" class="px-4 py-2 text-gray-500">--}}
{{--                            <div class="flex justify-center items-center space-x-2">--}}
{{--                                <x-pupi.icon.database/>--}}
{{--                                <span class="py-0 font-medium text-gray-400 dark:text-gray-400 text-lg">--}}
{{--                                {{ __('No entries found...') }}--}}
{{--                            </span>--}}
{{--                            </div>--}}
{{--                        </flux:cell>--}}
{{--                    </flux:row>--}}
{{--                @endforelse--}}
{{--            </flux:rows>--}}
{{--        </flux:table>--}}
{{--        {{ $stages->links() }}--}}
    </flux:modal>
</div>
