<x-pupi.table2.tr.body>
    <x-pupi.table2.tr.cell1>
        {{ $contact->name }}
    </x-pupi.table2.tr.cell1>
    <x-pupi.table2.tr.cell>
        {{ __($contact->gender?->label() ?? '-') }}
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.cell>
        {{ $contact->related ?? '-' }}
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.cell>
        {{ $contact->phone ?? '-' }}
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.cell>
        {{ $contact->email ?? '-' }}
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.action>
        <flux:dropdown align="end" offset="-15">
            <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"
                         size="sm"
                         variant="ghost" inset="top bottom"/>

            <flux:menu class="min-w-32">
                <flux:modal.trigger name="edit-contact-{{ $contact->id }}">
                    <flux:menu.item
                        icon="pencil">
                        {{ __('Edit') }}
                    </flux:menu.item>
                </flux:modal.trigger>

                <flux:separator class="my-1"/>

                <flux:menu.item
                    wire:click="$dispatch('deleted')"
                    wire:confirm="{{ __('Are you sure you want to remove this contact?') }}"
                    icon="trash"
                    variant="danger">
                    {{ __('Delete') }}
                </flux:menu.item>

            </flux:menu>
        </flux:dropdown>
    </x-pupi.table2.tr.action>

    <td class="hidden sm:table-cell">
        <flux:modal
            name="edit-contact-{{ $contact->id }}"
            variant="flyout"
            position="left"
            class="space-y-6 lg:min-w-3xl">
            <div>
                <flux:heading size="lg">{{ __('Edit Emergency Contact') }}</flux:heading>
                <flux:subheading>{{ __('Update emergency contact information') }}</flux:subheading>
            </div>

            <!-- Formular: Contact Data -->
            <form wire:submit="updateContact" class="space-y-4">
                <!-- Personal Information Section -->
                <div class="py-4">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                        <!-- Gender -->
                        <div class="sm:col-span-4">
                            <x-pupi.input.group
                                label="{{ __('Gender') }}"
                                for="gender"
                                badge="{{ __('Optional') }}"
                                :error="$errors->first('gender')"
                                model="gender"
                                help-text="{{ __('') }}">

                                <flux:select
                                    class="mt-2"
                                    wire:model="gender"
                                    id="gender"
                                    variant="listbox"
                                    placeholder="{{ __('Select gender') }}">

                                    @foreach($this->genderOptions() as $genderOption)
                                        <flux:option
                                            wire:key="gender-option-{{ $genderOption['value'] }}"
                                            value="{{ $genderOption['value'] }}">
                                            <span>{{ $genderOption['label'] }}</span>
                                        </flux:option>
                                    @endforeach

                                </flux:select>

                            </x-pupi.input.group>
                        </div>

                        <!-- Full Name -->
                        <div class="sm:col-span-5">
                            <x-pupi.input.group
                                label="{{ __('Full Name') }}"
                                for="name"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('name')"
                                help-text=""
                                model="name">

                                <x-pupi.input.text
                                    autofocus
                                    wire:model="name"
                                    id="name"
                                    name="name"
                                    placeholder="{{ __('Contact Name') }}"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- Relationship -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Relationship') }}"
                                for="related"
                                badge="{{ __('Optional') }}"
                                model="related"
                                :error="$errors->first('related')"
                                help-text="{{ __('e.g. Spouse, Parent, Sibling') }}">

                                <x-pupi.input.text
                                    wire:model="related"
                                    id="related"
                                    name="related"
                                    placeholder="{{ __('Relationship') }}"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- Phone -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Phone Number') }}"
                                for="phone"
                                model="phone"
                                badge="{{ __('Required') }}"
                                error="{{ $errors->first('phone') }}">

                                <x-pupi.input.text
                                    wire:model="phone"
                                    name="phone"
                                    id="phone"
                                    placeholder="{{ __('+41 XX XXX XX XX') }}"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-6">
                            <x-pupi.input.group
                                label="{{ __('Email Address') }}"
                                for="email"
                                badge="{{ __('Optional') }}"
                                :error="$errors->first('email')"
                                help-text="{{ __('Valid email address for emergency contact') }}">

                                <x-pupi.input.text
                                    wire:model="email"
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="{{ __('contact@example.com') }}"
                                />

                            </x-pupi.input.group>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                    <flux:button
                        wire:click="closeEditContactModal"
                        type="button"
                        variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button
                        type="submit"
                        variant="primary">
                        {{ __('Save Changes') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    </td>
</x-pupi.table2.tr.body>
