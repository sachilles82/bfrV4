{{--<div wire:ignore.self>--}}
{{--    <flux:modal--}}
{{--        name="edit-child"--}}
{{--        variant="flyout"--}}
{{--        position="left"--}}
{{--        class="space-y-6 lg:min-w-3xl"--}}
{{--    >--}}
{{--        <div>--}}
{{--            <flux:heading size="lg">{{ __('Edit Child') }}</flux:heading>--}}
{{--            <flux:subheading>{{ __('Update child information for family allowance') }}</flux:subheading>--}}
{{--        </div>--}}

{{--        <!-- Formular: Child Data -->--}}
{{--        <form wire:submit.prevent="updateChild">--}}

{{--            <div wire:loading--}}
{{--                 wire:target.except="updateChild"--}}
{{--                 class="space-y-4 relative w-full inset-0 backdrop-blur-sm z-10 overflow-auto">--}}

{{--                <!-- Personal Information Skeleton -->--}}
{{--                <div class="py-4">--}}
{{--                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">--}}
{{--                        <!-- Gender -->--}}
{{--                        <div class="sm:col-span-4">--}}
{{--                            <x-pupi.placeholder.input/>--}}
{{--                        </div>--}}

{{--                        <!-- First Name -->--}}
{{--                        <div class="sm:col-span-5">--}}
{{--                            <x-pupi.placeholder.input/>--}}
{{--                        </div>--}}

{{--                        <!-- Last Name -->--}}
{{--                        <div class="sm:col-span-3">--}}
{{--                            <x-pupi.placeholder.input/>--}}
{{--                        </div>--}}

{{--                        <!-- Email -->--}}
{{--                        <div class="sm:col-span-3">--}}
{{--                            <x-pupi.placeholder.input/>--}}
{{--                        </div>--}}

{{--                        <!-- Teams -->--}}
{{--                        <div class="sm:col-span-3">--}}
{{--                            <x-pupi.placeholder.input/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- Form Buttons -->--}}
{{--                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">--}}
{{--                    <x-pupi.placeholder.buttons/>--}}
{{--                </div>--}}

{{--            </div>--}}
{{--            <!-- Personal Information Section -->--}}
{{--            <div wire:loading.remove--}}
{{--                 wire:target.except="updateChild"--}}
{{--                 class="space-y-4 relative"--}}
{{--            >--}}

{{--                <!-- Personal Information Section -->--}}
{{--                <div class="py-4">--}}
{{--                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">--}}

{{--                        <!-- Gender -->--}}
{{--                        <div class="sm:col-span-4">--}}
{{--                            <x-pupi.input.group--}}
{{--                                label="{{ __('Gender') }}"--}}
{{--                                for="gender"--}}
{{--                                badge="{{ __('Optional') }}"--}}
{{--                                :error="$errors->first('gender')"--}}
{{--                                model="gender"--}}
{{--                                help-text="">--}}

{{--                                <flux:select--}}
{{--                                    class="mt-2"--}}
{{--                                    wire:model="gender"--}}
{{--                                    id="gender"--}}
{{--                                    variant="listbox"--}}
{{--                                    placeholder="{{ __('Select gender') }}">--}}

{{--                                    @foreach($this->genderOptions() as $genderOption)--}}
{{--                                        <flux:option--}}
{{--                                            wire:key="gender-option-{{ $genderOption['value'] }}"--}}
{{--                                            value="{{ $genderOption['value'] }}">--}}
{{--                                            <span>{{ $genderOption['label'] }}</span>--}}
{{--                                        </flux:option>--}}
{{--                                    @endforeach--}}

{{--                                </flux:select>--}}

{{--                            </x-pupi.input.group>--}}
{{--                        </div>--}}

{{--                        <!-- Full Name -->--}}
{{--                        <div class="sm:col-span-5">--}}
{{--                            <x-pupi.input.group--}}
{{--                                label="{{ __('Full Name') }}"--}}
{{--                                for="name"--}}
{{--                                badge="{{ __('Required') }}"--}}
{{--                                :error="$errors->first('name')"--}}
{{--                                help-text=""--}}
{{--                                model="name">--}}

{{--                                <x-pupi.input.text--}}
{{--                                    wire:model="name"--}}
{{--                                    id="name"--}}
{{--                                    placeholder="{{ __('Your Child Name') }}"--}}
{{--                                />--}}

{{--                            </x-pupi.input.group>--}}
{{--                        </div>--}}

{{--                        <!-- Birthdate -->--}}
{{--                        <div class="sm:col-span-3">--}}
{{--                            <x-pupi.input.group--}}
{{--                                label="{{ __('Birthdate') }}"--}}
{{--                                for="birthdate"--}}
{{--                                badge="{{ __('Optional') }}"--}}
{{--                                model="birthdate"--}}
{{--                                :error="$errors->first('birthdate')"--}}
{{--                                help-text="{{ __('Child must be under 25 years old') }}">--}}

{{--                                <flux:input--}}
{{--                                    wire:model="birthdate"--}}
{{--                                    id="birthdate"--}}
{{--                                    type="date"--}}
{{--                                    max="{{ now()->format('Y-m-d') }}"--}}
{{--                                    min="{{ now()->subYears(25)->format('Y-m-d') }}"--}}
{{--                                    class="mt-2"--}}
{{--                                />--}}

{{--                            </x-pupi.input.group>--}}
{{--                        </div>--}}

{{--                        <!-- AHV Number -->--}}
{{--                        <div class="sm:col-span-3">--}}
{{--                            <x-pupi.input.group--}}
{{--                                label="{{ __('AHV Number') }}"--}}
{{--                                for="ahv_number"--}}
{{--                                badge="{{ __('Optional') }}"--}}
{{--                                :error="$errors->first('ahv_number')"--}}
{{--                                help-text="{{ __('Format: 756.xxxx.xxxx.xx') }}"--}}
{{--                                model="ahv_number">--}}

{{--                                <x-pupi.input.text--}}
{{--                                    wire:model="ahv_number"--}}
{{--                                    id="ahv_number"--}}
{{--                                    placeholder="756.1234.5678.90"--}}
{{--                                    x-mask="999.9999.9999.99"--}}
{{--                                />--}}

{{--                            </x-pupi.input.group>--}}
{{--                        </div>--}}

{{--                        <!-- Valid Until -->--}}
{{--                        <div class="sm:col-span-3">--}}
{{--                            <x-pupi.input.group--}}
{{--                                label="{{ __('Valid Until') }}"--}}
{{--                                for="valid_until"--}}
{{--                                badge="{{ __('Optional') }}"--}}
{{--                                model="valid_until"--}}
{{--                                :error="$errors->first('valid_until')"--}}
{{--                                help-text="{{ __('Automatically set to 18th birthday if not specified') }}">--}}

{{--                                <flux:input--}}
{{--                                    wire:model="valid_until"--}}
{{--                                    id="valid_until"--}}
{{--                                    type="date"--}}
{{--                                    min="{{ now()->format('Y-m-d') }}"--}}
{{--                                    class="mt-2"--}}
{{--                                />--}}

{{--                            </x-pupi.input.group>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- Form Buttons -->--}}
{{--                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">--}}
{{--                    <flux:button wire:click="closeEditChildModal" type="button" variant="ghost">--}}
{{--                        {{ __('Cancel') }}--}}
{{--                    </flux:button>--}}
{{--                    <flux:button type="submit" variant="primary">--}}
{{--                        {{ __('Update Child') }}--}}
{{--                    </flux:button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </flux:modal>--}}
{{--</div>--}}


<div>
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

            <button wire:click="$dispatch('deleted')" type="button" class="text-center rounded-xl bg-red-500 text-white px-6 py-2 font-semibold disabled:cursor-not-allowed disabled:opacity-50">Delete</button>

        </x-pupi.table2.tr.action>
        <!-- Edit Modal -->
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
                                badge="{{ __('Optional') }}"
                                :error="$errors->first('form.gender')"
                                model="form.gender"
                                help-text="">

                                <flux:select
                                    class="mt-2"
                                    wire:model="form.gender"
                                    id="form.gender"
                                    variant="listbox"
                                    placeholder="{{ __('Select gender') }}">

                                    @foreach($this->genderOptions() as $genderOption)
                                        <flux:option
                                            wire:key="gender-option-{{ $child->id }}-{{ $genderOption['value'] }}"
                                            value="{{ $genderOption['value'] }}"
                                            {{--                                        :selected="$gender === $genderOption['value']"--}}
                                        >
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
                        variant="primary"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Save Changes') }}</span>
                        <span wire:loading>{{ __('Saving...') }}</span>
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    </x-pupi.table2.tr.body>

</div>
