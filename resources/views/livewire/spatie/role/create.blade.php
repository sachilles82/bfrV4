<div>
    <flux:modal.trigger name="role-add">
        {{--            <x-pupi.button.create/>--}}
        <flux:button variant="primary" class="ml-2">{{ __('Add Role') }}</flux:button>
    </flux:modal.trigger>

    <flux:modal name="role-add"
                variant="flyout" class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add New Role') }}</flux:heading>
                <flux:subheading>{{ __('save a new role in database') }}</flux:subheading>
            </div>
            <div class="pt-4 pb-8">
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Role Name') }}"
                            for="name"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('name')"
                            help-text="{{ __('') }}"
                            model="name"
                        >
                            <x-pupi.input.text autofocus
                                               wire:model="name"
                                               name="name"
                                               id="name"
                                               placeholder="{{ __('Employee Role') }}"
                            />
                        </x-pupi.input.group>
                    </div>
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Access Panel') }}"
                            for="access"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('access')"
                            help-text="{{ __('') }}"
                            model="access"
                        >
                            <flux:select class="mt-2"
                                         wire:model="access"
                                         placeholder="{{ __('Choose Access') }}"
                                         variant="listbox"
                            >
                                @foreach($accessOptions as $option)
                                    <flux:option value="{{ $option->value }}">
                                        {{ $option->label() }}
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>
                    <div class="col-span-full">
                        <x-pupi.input.group
                            label="{{ __('Description') }}"
                            for="description"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('description')"
                            help-text="{{ __('') }}"
                            model="description"
                        >
                            <x-pupi.input.textarea rows="2"
                                                   wire:model="description"
                                                   name="description"
                                                   id="description"
                                                   placeholder="{{ __('Role with Full Access') }}"
                            />
                        </x-pupi.input.group>
                    </div>
                </div>
            </div>


            <div class="flex">
                <flux:spacer/>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>

        </form>
    </flux:modal>
</div>
