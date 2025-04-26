<div>
    <flux:modal.trigger name="create-stage">
        <x-pupi.button.open-manager/>
    </flux:modal.trigger>

    <flux:modal name="create-stage" variant="flyout" class="w-96">
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

        <!-- Tabelle mit vorhandenen Stages -->
        <flux:table>
            <flux:columns>
                <flux:column class="text-sm! font-semibold">{{ __('Stage') }}</flux:column>
                <flux:column class="text-sm! font-semibold">{{ __('Actions') }}</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse ($stages as $stage)
                    <flux:row :key="$stage->id" class="hover:bg-gray-100">
                        <flux:cell>
                            <span class="text-sm font-medium">{{ $stage->name }}</span>
                        </flux:cell>
                        <flux:cell>
                            <flux:dropdown align="end" offset="-15">
                                <flux:button
                                    class="hover:bg-gray-200/75"
                                    icon="ellipsis-horizontal"
                                    size="sm"
                                    variant="ghost"
                                    inset="top bottom"
                                />
                                <flux:menu class="min-w-32">
                                    <flux:menu.item
                                        wire:click="editStage({{ $stage->id }})"
                                        icon="pencil-square"
                                    >
                                        {{ __('Edit') }}
                                    </flux:menu.item>
                                    <flux:menu.item
                                        wire:click="deleteStage({{ $stage->id }})"
                                        wire:confirm="{{ __('Are you sure you want to remove this stage?') }}"
                                        icon="trash"
                                        variant="danger"
                                    >
                                        {{ __('Delete') }}
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:cell>
                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="2" class="px-4 py-2 text-gray-500">
                            <div class="flex justify-center items-center space-x-2">
                                <x-pupi.icon.database/>
                                <span class="py-0 font-medium text-gray-400 dark:text-gray-400 text-lg">
                                {{ __('No entries found...') }}
                            </span>
                            </div>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>
{{--        {{ $stages->links() }}--}}
    </flux:modal>
</div>
