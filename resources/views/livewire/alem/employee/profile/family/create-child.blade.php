<div>
    <!-- Button Container -->
    <x-pupi.button.container>
        <flux:modal.trigger name="create-child">
            <flux:button
                variant="primary">
                {{ __('Add Child') }}
            </flux:button>
        </flux:modal.trigger>
    </x-pupi.button.container>

    <flux:modal
        name="create-child"
        variant="flyout"
        position="left"
        class="space-y-6 lg:min-w-3xl"
    >
        <div>
            <flux:heading size="lg">{{ __('Add Child') }}</flux:heading>
            <flux:subheading>{{ __('Register a child for family allowance') }}</flux:subheading>
        </div>

        <!-- Formular: Child Data -->
        <form wire:submit="add" class="space-y-4">

            <!-- Personal Information Section -->
            <div class="py-4">
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    <!-- Gender -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Gender') }}"
                            for="gender"
                            badge="{{ __('Required') }}"
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

                    <!-- First Name -->
                    <div class="sm:col-span-5">
                        <x-pupi.input.group
                            label="{{ __('Full Name') }}"
                            for="name"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('name')"
                            help-text="{{ __('') }}"
                            model="name">

                            <x-pupi.input.text
                                wire:model="name"
                                id="name"
                                autofocus
                                placeholder="{{ __('Your Child Name') }}"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Birthdate -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Birthdate') }}"
                            for="birthdate"
                            badge="{{ __('Required') }}"
                            model="birthdate"
                            :error="$errors->first('birthdate')"
                            help-text="{{ __('Child must be under 25 years old') }}">

                            <flux:input
                                wire:model="birthdate"
                                id="birthdate"
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
                            for="ahv_number"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('ahv_number')"
                            help-text="{{ __('Format: 756.xxxx.xxxx.xx') }}"
                            model="ahv_number">

                            <x-pupi.input.text
                                wire:model="ahv_number"
                                x-mask="756.9999.9999.99"
                                name="ahv_number"
                                id="ahv_number"
                                placeholder="{{ __('756.XXXX.XXXX.XX') }}"
                            />

                        </x-pupi.input.group>
                    </div>
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                <flux:button wire:click="closeCreateChildModal" type="button" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Add Child') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
