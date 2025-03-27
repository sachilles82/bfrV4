<div wire:ignore.self>
    <flux:modal.trigger name="create-department">
        @if($displayMode === 'dropdown')
            {{-- Open Manager Button für Dropdown-Ansicht --}}
            <x-pupi.button.open-manager/>
        @else
            {{-- Standard-Button für die Index-Seite --}}
            <div class="ml-auto flex items-center gap-x-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-pointer">
                <x-pupi.icon.create class="-ml-1.5 size-5"/>
                {{ __('Create') }}
            </div>
        @endif
    </flux:modal.trigger>

    <flux:modal name="create-department" variant="flyout" position="left" class="space-y-6"
                wire:model="showModal">
        <div>
            <flux:heading size="lg">{{ __('Create Department') }}</flux:heading>
            <flux:subheading>{{ __('Fill out the details to create a new department') }}</flux:subheading>
        </div>

        <!-- Formular für Abteilungsdaten -->
        <form wire:submit.prevent="save" class="space-y-4">
            {{--            <div wire:loading class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-gray-900/50 backdrop-blur-xs rounded-lg"></div>--}}

            <!-- Department Information Section -->
            <div class="py-4">
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-3">
                    <!-- Department Name -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Department Name') }}"
                            for="name"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('name')"
                            model="name"
                            help-text="{{ __('Enter the name of the department') }}">
                            <flux:input
                                class="mt-2"
                                wire:model="name"
                                id="name"
                                type="text"
                                placeholder="{{ __('Enter department name') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Description') }}"
                            for="description"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('description')"
                            model="description"
                            help-text="{{ __('Provide a brief description of the department') }}">
                            <flux:textarea
                                class="mt-2"
                                wire:model="description"
                                id="description"
                                placeholder="{{ __('Enter department description') }}"
                                rows="3"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Model Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Account Status') }}"
                            for="model_status"
                            badge="{{ __('Required') }}"
                            model="model_status"
                            help-text="{{ __('') }}"
                            :error="$errors->first('model_status')">
                            <flux:select
                                wire:model="model_status"
                                id="model_status"
                                name="model_status"
                                variant="listbox"
                                placeholder="{{ __('Account Status') }}">
                                @foreach($this->modelStatusOptions as $status)
                                    <flux:option value="{{ $status['value'] }}">
                                        <div class="flex items-center">
                                            <span class="mr-2">
                                                <x-dynamic-component
                                                    :component="$status['icon'] ?? 'heroicon-o-question-mark-circle'"
                                                    class="{{ $status['colors'] ?? '' }}"/>
                                            </span>
                                            <span>{{ $status['label'] }}</span>
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
                <flux:button wire:click="resetForm" type="button" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
