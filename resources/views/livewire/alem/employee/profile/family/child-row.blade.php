<x-pupi.table2.tr.body>
    <x-pupi.table2.tr.cell1>
        {{ $child->name }}
    </x-pupi.table2.tr.cell1>
    <x-pupi.table2.tr.cell>
        {{ __($child->gender?->label() ?? '-') }}
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.cell>
        <flux:tooltip class="cursor-default"
                      content="{{ __('Age: ') . $child->age . ' ' . __('years') }}"
                      position="top">
            {{ $child->birthdate?->format('d.m.Y') ?? '-' }}
        </flux:tooltip>
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.cell>
        {{ $child->age }} {{ __('years') }}
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.cell>
        {{ $child->ahv_number ?? '-' }}
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.cell>
        @if($child->valid_until)
            {{ $child->valid_until->format('d.m.Y') }}
            @if($child->is_valid)
                <span class="text-green-600 dark:text-green-400">✓</span>
            @else
                <span class="text-red-600 dark:text-red-400">✗</span>
            @endif
        @else
            -
        @endif
    </x-pupi.table2.tr.cell>
    <x-pupi.table2.tr.action>
        <flux:dropdown align="end" offset="-15">
            <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"
                         size="sm"
                         variant="ghost" inset="top bottom"/>

            <flux:menu class="min-w-32">
                <flux:modal.trigger name="edit-child-{{ $child->id }}">
                    <flux:menu.item
                        icon="pencil">
                        {{ __('Edit') }}
                    </flux:menu.item>
                </flux:modal.trigger>

                <flux:separator class="my-1"/>

                <flux:menu.item
                    wire:click="$dispatch('deleted')"
                    wire:confirm="{{ __('Are you sure you want to remove this child?') }}"
                    icon="trash"
                    variant="danger">
                    {{ __('Delete') }}
                </flux:menu.item>

            </flux:menu>
        </flux:dropdown>
    </x-pupi.table2.tr.action>

    <td class="">

        <flux:modal
            name="edit-child-{{ $child->id }}"
            variant="flyout"
            position="left"
            class="space-y-6 lg:min-w-3xl">
            <div>
                <flux:heading size="lg">{{ __('Edit Child') }}</flux:heading>
                <flux:subheading>{{ __('Update child information for family allowance') }}</flux:subheading>
            </div>

            <!-- Formular: Child Data -->
            <form wire:submit="save" class="space-y-4">
                <!-- Personal Information Section -->
                <div class="py-4">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                        <!-- Gender -->
                        <div class="sm:col-span-4">
                            <x-pupi.input.group
                                label="{{ __('Gender') }}"
                                for="form.gender"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('gender')"
                                model="form.gender"
                                help-text="{{ __('') }}">

                                <flux:select
                                    class="mt-2"
                                    wire:model="form.gender"
                                    id="form.gender"
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
                                for="form.name"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('form.name')"
                                help-text=""
                                model="form.name">

                                <x-pupi.input.text
                                    autofocus
                                    wire:model="form.name"
                                    :value="$form->name"
                                    id="form.name"
                                    placeholder="{{ __('Child Name') }}"
                                />

                            </x-pupi.input.group>
                        </div>


                        <!-- Birthdate -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Birthdate') }}"
                                for="form.birthdate"
                                badge="{{ __('Required') }}"
                                model="form.birthdate"
                                :value="$form->birthdate"
                                :error="$errors->first('form.birthdate')"
                                help-text="{{ __('Child must be under 25 years old') }}">

                                <flux:input
                                    wire:model="form.birthdate"
                                    id="form.birthdate"
                                    type="date"
                                    max="{{ now()->format('Y-m-d') }}"
                                    min="{{ now()->subYears(25)->format('Y-m-d') }}"
                                    class="mt-2"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- AHV Number -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('AHV Number') }}"
                                for="form.ahv_number"
                                badge="{{ __('Optional') }}"
                                :error="$errors->first('form.ahv_number')"
                                help-text="{{ __('Format: 756.xxxx.xxxx.xx') }}"
                                model="form.ahv_number">

                                <x-pupi.input.text
                                    wire:model="form.ahv_number"
                                    :value="$form->ahv_number"
                                    id="form.ahv_number"
                                    placeholder="756.1234.5678.90"
                                    x-mask="999.9999.9999.99"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- Valid Until -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Valid Until') }}"
                                for="form.valid_until"
                                badge="{{ __('Optional') }}"
                                model="form.valid_until"
                                :error="$errors->first('form.valid_until')"
                                help-text="{{ __('Automatically set to 18th birthday if not specified') }}">

                                <flux:input
                                    wire:model="form.valid_until"
                                    :value="$form->valid_until"
                                    id="form.valid_until"
                                    type="date"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="mt-2"
                                />

                            </x-pupi.input.group>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                    <flux:button
                        wire:click="closeEditModal"
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
