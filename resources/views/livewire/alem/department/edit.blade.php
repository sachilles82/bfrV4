<div wire:ignore.self>
    <flux:modal name="edit-department" variant="flyout" position="left" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Edit Department') }}</flux:heading>
            <flux:subheading>{{ __('Update department information') }}</flux:subheading>
        </div>

        <form wire:submit.prevent="updateDepartment" class="space-y-4">

            <div class="py-4">
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <!-- Department Name -->
                    <div class="sm:col-span-6">
                        <x-pupi.input.group
                            label="{{ __('Department Name') }}"
                            for="name"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('name')"
                            help-text="{{ __('') }}"
                            model="name">
                            <x-pupi.input.text
                                wire:model="name"
                                id="name"
                                placeholder="{{ __('Enter department name') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-6">
                        <x-pupi.input.group
                            label="{{ __('Description') }}"
                            for="description"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('description')"
                            help-text="{{ __('Provide a brief description of this department') }}"
                            model="description">
                            <x-pupi.input.textarea
                                wire:model="description"
                                id="description"
                                placeholder="{{ __('Enter department description') }}"
                                rows="3"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Status Selection -->
                    <div class="sm:col-span-6">
                        <x-pupi.input.group
                            label="{{ __('Status') }}"
                            for="model_status"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('model_status')"
                            model="model_status"
                            help-text="{{ __('Select the current status of this department') }}"
                        >
                            <flux:select
                                class="mt-2"
                                wire:model="model_status"
                                id="model_status"
                                variant="listbox">
                                @foreach($modelStatusOptions as $statusOption)
                                    <flux:option value="{{ $statusOption['value'] }}">
                                        <div class="flex items-center">
                                            <span class="mr-2">
                                                <x-dynamic-component
                                                    :component="$statusOption['icon']"
                                                    class="{{ $statusOption['colors'] ?? '' }}"/>
                                            </span>
                                            <span>{{ $statusOption['label'] }}</span>
                                        </div>
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                <flux:button wire:click="closeModal" type="button" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit">
                    {{ __('Update') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
