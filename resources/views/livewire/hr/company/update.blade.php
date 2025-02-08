<x-pupi.layout.form>

    <x-slot:title>
        {{ __('Company Information') }}
    </x-slot:title>

    <x-slot:description>
        {{__('Here are stored the company information') }}
    </x-slot:description>

    <x-slot name="form">

        <!-- Company Form -->
        <form wire:submit.prevent="save">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Company Name') }}"
                            for="company_name"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('company_name')"
                            help-text="{{ __('') }}"
                            model="company_name"
                        >
                            <x-pupi.input.text
                                wire:model="company_name"
                                name="company_name"
                                id="company_name"
                                placeholder="{{ __('Company Name') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:select
                                wire:model="company_type"
                                label="{{ __('Company Type') }}"
                                id="company_type"
                                name="company_type"
                                variant="listbox"
                                placeholder="{{ __('Choose Company type') }}"
                            >
                                @foreach(App\Enums\Company\CompanyType::options() as $value => $label)
                                    <flux:option value="{{ $value }}">
                                        {{ $label }}
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="sm:col-span-4">
                        <flux:select
                            wire:model="industry_id"
                            label="{{ __('Industry') }}"
                            variant="listbox"
                            searchable
                            placeholder="{{ __('Choose an Industry') }}"
                        >
                            @foreach(\App\Models\HR\Industry::all() as $industry)
                                <flux:option value="{{ $industry->id }}">{{ $industry->name }}</flux:option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:label>{{ __('Company Size') }}</flux:label>
                            <flux:select
                                wire:model="company_size"
                                id="company_size"
                                name="company_size"
                                variant="listbox"
                                placeholder="{{ __('Choose Company Size') }}"
                            >
                                @foreach(App\Enums\Company\CompanySize::options() as $value => $label)
                                    <flux:option value="{{ $value }}">
                                        {{ $label }}
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('UID Number') }}"
                            for="register_number"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('register_number')"
                            help-text="{{ __('') }}"
                            model="register_number"
                        >
                            <x-pupi.input.text
                                wire:model="register_number"
                                x-mask="CHE- 999.999.999"
                                name="register_number"
                                id="register_number"
                                placeholder="{{ __('CHE-123.456.789') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Company URL') }}"
                            for="company_url"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('company_url')"
                            help-text="{{ __('') }}"
                            model="company_url"
                        >
                            <div class="mt-2 flex rounded-md shadow-sm">
                                <input
                                    wire:model="company_url"
                                    type="text"
                                    name="company_url"
                                    id="company_url"
                                    class="block w-full min-w-0 flex-1 shadow-sm rounded-none rounded-l-md dark:bg-white/5 border-0 py-1.5 text-gray-900 dark:text-white dark:focus-within:ring-inset dark:focus-within:ring-indigo-500 dark:ring-white/10 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="muster-gartenbau">
                                <span
                                    class="bg-gray-50 inline-flex items-center rounded-r-md border border-l-0 border-gray-300 dark:bg-white/10 dark:border-gray-700 px-3 dark:text-gray-400 text-gray-600 sm:text-sm sm:leading-6">
                                    .reportix.app
                                </span>
                            </div>
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Telefon 1') }}"
                            for="phone_1"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('phone_1')"
                            help-text="{{ __('') }}"
                            model="phone_1"
                        >
                            <x-pupi.input.text
                                wire:model="phone_1"
{{--                                x-mask="+41 99 999 99 99"--}}
                                name="phone_1"
                                id="phone_1"
                                placeholder="{{ __('+41 44 401 11 42') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Telefon 2') }}"
                            for="phone_2"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('phone_2')"
                            help-text="{{ __('') }}"
                            model="phone_2"
                        >
                            <x-pupi.input.text
                                wire:model="phone_2"
{{--                                x-mask="+41 99 999 99 99"--}}
                                name="phone_2"
                                id="phone_2"
                                placeholder="{{ __('+41 76 699 23 24') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Company Email') }}"
                            for="email"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('email')"
                            help-text="{{ __('') }}"
                            model="email"
                        >
                            <x-pupi.input.text
                                wire:model="email"
                                name="email"
                                id="email"
                                placeholder="{{ __('info@meinefirma.ch') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-4">
                        <div class="sm:col-span-4">
                            <x-pupi.input.group
                                label="{{ __('') }}"
                                for="owner_name"
                                :error="$errors->first('owner_name')"
                                help-text="{{ __('') }}"
                                model="ownerName"
                            >
                                <div class="flex items-center justify-between">
                                    <!-- Label und Tooltip -->
                                    <div class="flex items-center gap-2 -mb-1">
                                        <label for="owner_name"
                                               class="block text-sm font-medium leading-6 text-gray-900 dark:text-white">
                                            {{ __('Account Owner') }}
                                        </label>

                                        <flux:tooltip toggleable>
                                            <flux:button icon="information-circle" size="sm" variant="ghost"/>
                                            <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                                <p>{{ __('This is the owner of the account') }}</p>
                                            </flux:tooltip.content>
                                        </flux:tooltip>
                                    </div>
                                </div>

                                <!-- Input Feld -->
                                <x-pupi.input.text
                                    wire:model="ownerName"
                                    name="owner_name"
                                    id="owner_name"
                                    placeholder="{{ __('Account Owner') }}"
                                    readonly
                                />
                            </x-pupi.input.group>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Container with Action Buttons -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit
                />
            </x-pupi.button.container>
        </form>

    </x-slot>

</x-pupi.layout.form>
