<flux:modal name="create-profession" class="w-96">
    <!-- Modal-Header -->
    <div>
        <flux:heading size="lg">
            {{ __('Profession Manager') }}
        </flux:heading>
        <flux:subheading>
            {{ $editing ? __('Edit the profession details.') : __('Fill out the details to create a new profession.') }}
        </flux:subheading>
    </div>

    <!-- Formular: Create/Update Profession -->
    <form wire:submit.prevent="saveProfession" class="space-y-4">
        <div class="sm:col-span-3">
            <x-pupi.input.group
                label="{{ __('Profession') }}"
                for="name"
                badge="{{ __('Required') }}"
                :error="$errors->first('name')">
                <x-pupi.input.text wire:model.defer="name" id="name" placeholder="{{ __('Enter profession name') }}"/>
            </x-pupi.input.group>
        </div>

        <!-- Formular-Buttons -->
        {{--            <div class="flex space-x-4">--}}
        {{--                <x-pupi.button.fluxback type="button" wire:click="resetForm">--}}
        {{--                    {{ __('Cancel') }}--}}
        {{--                </x-pupi.button.fluxback>--}}
        {{--                <flux:button type="submit">--}}
        {{--                    {{ $editing ? __('Update Profession') : __('Create Profession') }}--}}
        {{--                </flux:button>--}}
        {{--            </div>--}}


        <div class="flex gap-2">
            <flux:spacer/>

            <flux:modal.close>
                <flux:button wire:click="resetForm" variant="ghost">Cancel</flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary">
                {{ $editing ? __('Update') : __('Create ') }}
            </flux:button>
        </div>


    </form>

    <!-- Tabelle mit vorhandenen Professions -->
    <flux:table>
        <flux:columns>
            <flux:column class="!text-sm font-semibold">{{ __('Profession') }}</flux:column>
            <flux:column class="!text-sm font-semibold">{{ __('Actions') }}</flux:column>
        </flux:columns>

        <flux:rows>
            @forelse ($professions as $profession)
                <flux:row :key="$profession->id" class="hover:bg-gray-100">
                    <flux:cell>
                        <span class="text-sm font-medium">{{ $profession->name }}</span>
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
                                    wire:click="editProfession({{ $profession->id }})"
                                    icon="pencil-square"
                                >
                                    {{ __('Edit') }}
                                </flux:menu.item>
                                <flux:menu.item
                                    wire:click="deleteProfession({{ $profession->id }})"
                                    wire:confirm="{{ __('Are you sure you want to remove this profession?') }}"
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
    {{ $professions->links() }}
</flux:modal>
