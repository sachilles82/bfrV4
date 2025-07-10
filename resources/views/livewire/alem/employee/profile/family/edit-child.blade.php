<div wire:ignore.self>
    <flux:modal
        name="edit-child"
        variant="flyout"
        position="left"
        class="space-y-6 lg:min-w-3xl"
    >
        <div>
            <flux:heading size="lg">{{ __('Edit Child') }}</flux:heading>
            <flux:subheading>{{ __('Update child information for family allowance') }}</flux:subheading>
        </div>

        <!-- Formular: Child Data -->
        <form wire:submit.prevent="updateChild" class="space-y-4">

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
                            help-text="">

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
                                wire:model="name"
                                id="name"
                                placeholder="{{ __('Your Child Name') }}"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Birthdate -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Birthdate') }}"
                            for="birthdate"
                            badge="{{ __('Optional') }}"
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
                                id="ahv_number"
                                placeholder="756.1234.5678.90"
                                x-mask="999.9999.9999.99"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Valid Until -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Valid Until') }}"
                            for="valid_until"
                            badge="{{ __('Optional') }}"
                            model="valid_until"
                            :error="$errors->first('valid_until')"
                            help-text="{{ __('Automatically set to 18th birthday if not specified') }}">

                            <flux:input
                                wire:model="valid_until"
                                id="valid_until"
                                type="date"
                                min="{{ now()->format('Y-m-d') }}"
                                class="mt-2"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Changed Fields Indicator -->
                    @if($child && $this->hasAnyChanges())
                        <div class="sm:col-span-6">
                            <div class="rounded-md bg-yellow-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">
                                            {{ __('Unsaved changes') }}
                                        </h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>{{ __('You have made changes that have not been saved yet.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                <flux:button wire:click="closeEditChildModal" type="button" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Update Child') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
