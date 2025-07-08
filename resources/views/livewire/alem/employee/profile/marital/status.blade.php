<div>
    <x-pupi.layout.form>
        <x-slot:title>
            {{ __('Marital Status') }}
        </x-slot:title>

        <x-slot:description>
            {{ __('Update the marital data') }}
        </x-slot:description>

        <x-slot name="form">
            {{--            <div class="">--}}
            {{--                <section aria-labelledby="applicant-information-title">--}}
            {{--                    <div class="bg-white dark:bg-gray-900 shadow-xs ring-1 ring-gray-900/5 sm:rounded-md md:col-span-2">--}}
            {{--                        <div class="px-4 py-5 sm:px-6">--}}
            {{--                            <h2 id="applicant-information-title" class="text-base/7 font-semibold dark:text-white text-gray-900">Marital Stauts</h2>--}}
            {{--                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Marital status details.</p>--}}
            {{--                        </div>--}}
            {{--                        <div class="border-t border-gray-200 dark:border-white/5 px-4 py-5 sm:px-6">--}}
            {{--                            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">--}}
            {{--                                <div class="sm:col-span-1">--}}
            {{--                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-500">{{ __('Name Partner/in') }}</dt>--}}
            {{--                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">Ballmehina Xhaferi Alija</dd>--}}
            {{--                                </div>--}}
            {{--                                <div class="sm:col-span-1">--}}
            {{--                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-500">{{ __('Birthdate') }}</dt>--}}
            {{--                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">19.03.1990</dd>--}}
            {{--                                </div>--}}
            {{--                                <div class="sm:col-span-1">--}}
            {{--                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-500">{{ __('Civil Status') }}</dt>--}}
            {{--                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">verheiratet</dd>--}}
            {{--                                </div>--}}
            {{--                                <div class="sm:col-span-1">--}}
            {{--                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-500">{{ __('Single Parent') }}</dt>--}}
            {{--                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">NO</dd>--}}
            {{--                                </div>--}}
            {{--                            </dl>--}}
            {{--                        </div>--}}

            {{--                        <!-- Button Container -->--}}
            {{--                        <x-pupi.button.container>--}}
            {{--                            <x-pupi.button.fluxsubmit/>--}}
            {{--                        </x-pupi.button.container>--}}
            {{--                        <div>--}}
            {{--                            <a href="#" class="block bg-gray-50 px-4 py-4 text-center text-sm font-medium text-gray-500 hover:text-gray-700 sm:rounded-b-lg">Update Marital Status</a>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </section>--}}
            {{--            </div>--}}


            <form wire:submit.prevent="updateMaritalData">
                <div class="px-4 py-6 sm:p-8 relative">
                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                        <!-- Civil Status -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Civil Status') }}"
                                for="civil_status"
                                badge="{{ __('Optional') }}"
                                model="civil_status"
                                :error="$errors->first('civil_status')"
                                help-text="{{ __('') }}">

                                <flux:select
                                    class="!mt-2"
                                    wire:model="civil_status"
                                    name="civil_status"
                                    id="civil_status"
                                    variant="listbox"
                                    placeholder="{{ __('Select Civil Status') }}">

                                    @foreach($this->civilStatusOptions() as $civilStatusOption)
                                        <flux:option
                                            wire:key="civilStatus-option-{{ $civilStatusOption['value'] }}"
                                            value="{{ $civilStatusOption['value'] }}">
                                            <span>{{ $civilStatusOption['label'] }}</span>
                                        </flux:option>
                                    @endforeach

                                </flux:select>

                            </x-pupi.input.group>
                        </div>

                        <!-- Marriage Date -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Marriage Date') }}"
                                for="marriage_at"
                                badge="{{ __('Optional') }}"
                                :error="$errors->first('marriage_at')"
                                model="marriage_at"
                                help-text="{{ __('') }}"
                            >
                                <flux:date-picker
                                    wire:model="marriage_at"
                                    id="marriage_at"
                                    type="date">
                                    <x-slot name="trigger">
                                        <flux:date-picker.input class="mt-2"/>
                                    </x-slot>
                                </flux:date-picker>

                            </x-pupi.input.group>
                        </div>

                        <!-- Partner Name -->
                        <div class="sm:col-span-6">
                            <x-pupi.input.group
                                label="{{ __('Partner/in Full Name') }}"
                                for="name_partner"
                                badge="{{ __('Optional') }}"
                                model="name_partner"
                                help-text="{{ __('') }}"
                                :error="$errors->first('name_partner')">

                                <x-pupi.input.text
                                    wire:model="name_partner"
                                    name="name_partner"
                                    id="name_partner"
                                    placeholder="{{ __('Partner Full Name') }}"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- AHV Number -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Partner/in AHV Number') }}"
                                for="ahv_partner"
                                model="ahv_partner"
                                badge="{{ __('Optional') }}"
                                :error="$errors->first('ahv_partner')">

                                <x-pupi.input.text
                                    wire:model="ahv_partner"
                                    x-mask="756.9999.9999.99"
                                    name="ahv_partner"
                                    id="ahv_partner"
                                    placeholder="{{ __('756.XXXX.XXXX.XX') }}"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- Partner Birthdate -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Partner/in Birthdate') }}"
                                for="birthdate_partner"
                                badge="{{ __('Optional') }}"
                                :error="$errors->first('birthdate_partner')"
                                model="birthdate_partner"
                                help-text="{{ __('') }}"
                            >
                                <flux:date-picker
                                    wire:model="birthdate_partner"
                                    id="birthdate_partner"
                                    type="date">
                                    <x-slot name="trigger">
                                        <flux:date-picker.input class="mt-2"/>
                                    </x-slot>
                                </flux:date-picker>

                            </x-pupi.input.group>
                        </div>

                        <!-- Single Parent Toggle -->
                        <div class="sm:col-span-3"
                             x-data="{
                    singleParentEnabled: $wire.single_parent === true || $wire.single_parent === 1
                }"
                        >
                            <div class="divide-y divide-gray-200 dark:divide-white/10">
                                <div class="pb-2 flex items-center justify-between">
                        <span class="flex grow flex-col">
                            <span class="text-sm/6 font-medium text-gray-900 dark:text-white"
                                  id="single-parent-label">
                                {{ __('Parent Status') }}
                            </span>
                            <!-- Text based on toggle state -->
                            <span class="text-sm"
                                  :class="singleParentEnabled ? 'text-indigo-600 dark:text-indigo-500' : 'text-gray-500 dark:text-gray-400'">
                                <span x-show="!singleParentEnabled">{{ __('Not Single Parent') }}</span>
                                <span x-show="singleParentEnabled" x-cloak>{{ __('Is Single Parent') }}</span>
                            </span>
                        </span>

                                    <div class="flex items-center">
                                        <!-- Toggle Button -->
                                        <button
                                            @click="singleParentEnabled = !singleParentEnabled; $wire.single_parent = singleParentEnabled"
                                            type="button"
                                            class="relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none cursor-pointer"
                                            :class="{
                                    'bg-indigo-600 dark:bg-indigo-500': singleParentEnabled,
                                    'bg-gray-200 dark:bg-gray-700': !singleParentEnabled,
                                }"
                                            role="switch"
                                            :aria-checked="singleParentEnabled"
                                        >
                                <span
                                    class="pointer-events-none inline-block size-5 rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                                    :class="singleParentEnabled ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('single_parent')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Button Container -->
                <x-pupi.button.container>
                    <x-pupi.button.fluxsubmit/>
                </x-pupi.button.container>
            </form>

        </x-slot>
    </x-pupi.layout.form>
</div>
