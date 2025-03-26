<div>
    <flux:modal.trigger name="create-department">
        <x-pupi.button.open-manager/>
    </flux:modal.trigger>

    <flux:modal name="create-department"
                variant="flyout" class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add New Department') }}</flux:heading>
                <flux:subheading>{{ __('Create a new department in the database') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <!-- Department Name -->
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

                <!-- Description -->
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

                <!-- Status Selection -->
                <x-pupi.input.group
                    label="{{ __('Status') }}"
                    for="status"
                    badge="{{ __('Required') }}"
                    :error="$errors->first('status')"
                    model="status"
                    help-text="{{ __('Select the current status of this department') }}">
                    <flux:select
                        class="mt-2"
                        wire:model="status"
                        id="status"
                        variant="listbox">
                        @foreach($statuses as $statusOption)
                            <flux:option value="{{ $statusOption->value }}">
                                <div class="flex items-center">
                                    <span class="mr-2">
                                        <x-dynamic-component
                                            :component="$statusOption->icon()"
                                            class="h-4 w-4 {{ $statusOption->colors() ?? '' }}"/>
                                    </span>
                                    <span>{{ $statusOption->label() }}</span>
                                </div>
                            </flux:option>
                        @endforeach
                    </flux:select>
                </x-pupi.input.group>
            </div>

            <div class="flex justify-between">
                <flux:button wire:click="resetForm" variant="ghost">
                    {{ __('Reset') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Department Modal -->
    <flux:modal name="department-edit" variant="flyout" class="space-y-6">
        <form wire:submit="update" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Edit Department') }}</flux:heading>
                <flux:subheading>{{ __('Update this department') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <!-- Department Name -->
                <x-pupi.input.group
                    label="{{ __('Department Name') }}"
                    for="edit-name"
                    badge="{{ __('Required') }}"
                    :error="$errors->first('name')"
                    help-text="{{ __('') }}"
                    model="name">
                    <x-pupi.input.text 
                        wire:model="name" 
                        id="edit-name" 
                        placeholder="{{ __('Enter department name') }}"
                    />
                </x-pupi.input.group>

                <!-- Description -->
                <x-pupi.input.group
                    label="{{ __('Description') }}"
                    for="edit-description"
                    badge="{{ __('Optional') }}"
                    :error="$errors->first('description')"
                    help-text="{{ __('Provide a brief description of this department') }}"
                    model="description">
                    <x-pupi.input.textarea 
                        wire:model="description" 
                        id="edit-description" 
                        placeholder="{{ __('Enter department description') }}"
                        rows="3"
                    />
                </x-pupi.input.group>

                <!-- Status Selection -->
                <x-pupi.input.group
                    label="{{ __('Status') }}"
                    for="edit-status"
                    badge="{{ __('Required') }}"
                    :error="$errors->first('status')"
                    model="status"
                    help-text="{{ __('Select the current status of this department') }}">
                    <flux:select
                        class="mt-2"
                        wire:model="status"
                        id="edit-status"
                        variant="listbox">
                        @foreach($statuses as $statusOption)
                            <flux:option value="{{ $statusOption->value }}">
                                <div class="flex items-center">
                                    <span class="mr-2">
                                        <x-dynamic-component
                                            :component="$statusOption->icon()"
                                            class="h-4 w-4 {{ $statusOption->colors() ?? '' }}"/>
                                    </span>
                                    <span>{{ $statusOption->label() }}</span>
                                </div>
                            </flux:option>
                        @endforeach
                    </flux:select>
                </x-pupi.input.group>
            </div>

            <div class="flex justify-between">
                <flux:button wire:click="resetForm" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Update') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
