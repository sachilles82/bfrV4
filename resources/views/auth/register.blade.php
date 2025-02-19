<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo/>
        </x-slot>

        {{--        <x-validation-errors class="mb-4"/>--}}

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="space-y-4">
                <div x-data="{ selectedIndustry: null }">
                    <flux:label badge="{{ __('Required') }}">{{ __('Industry') }}</flux:label>
                    <flux:select variant="listbox" searchable placeholder="{{ __('Choose a Industry') }}"
                                 x-model="selectedIndustry">
                        @foreach(\App\Models\Alem\Industry::all() as $industry)
                            <flux:option value="{{ $industry->id }}">{{ $industry->name }}</flux:option>
                        @endforeach
                    </flux:select>
                    <input type="hidden" name="industry_id" :value="selectedIndustry">
                    <flux:error name="industry_id"/>
                </div>


                <div x-data="{ selectedSize: '{{ \App\Enums\Company\CompanySize::OneToFive }}' }">
                    <flux:field>
                        <flux:label badge="{{ __('Required') }}">{{ __('Company Size') }}</flux:label>

                        <flux:select
                                id="company_size"
                                name="company_size"
                                variant="listbox"
                                placeholder="{{ __('Company Size') }}"
                                @change="selectedSize = $event.target.value"
                        >
                            @foreach(App\Enums\Company\CompanySize::options() as $value => $label)
                                <flux:option
                                        value="{{ $value }}"
                                        :selected="$value === '1-5'"
                                >
                                    {{ $label }}
                                </flux:option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    <input type="hidden" name="company_size" x-model="selectedSize">
                </div>

                <flux:field>
                    <flux:label>{{ __('Company URL') }}</flux:label>

                    <flux:input.group>
                        <flux:input placeholder="meinefirma"/>

                        <flux:input.group.suffix>.reportix.app</flux:input.group.suffix>

                    </flux:input.group>

                    <flux:error name="website"/>
                </flux:field>
            </div>

            <div x-data="{ selectedValue: '{{ \App\Enums\Company\CompanyType::AG }}' }">
                <flux:radio.group label="Company Type" variant="segmented">
                    @foreach(\App\Enums\Company\CompanyType::options() as $value => $label)
                        <flux:radio
                                label="{{ $label }}"
                                value="{{ $value }}"
                                :checked="$value === 'gmbh'"
                                @click="selectedValue = '{{ $value }}'"
                        />
                    @endforeach
                </flux:radio.group>
                <input type="hidden" name="company_type" x-model="selectedValue">
            </div>


            <div class="mt-4">
                <x-label for="name" value="{{ __('Company Name') }}"/>
                <x-input id="name" class="block mt-1 w-full" type="text" name="company_name"
                         :value="old('company_name')" required
                         autofocus autocomplete="company_name"/>
            </div>

            <div class="mt-4">
                <x-label for="name" value="{{ __('Name') }}"/>
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                         autofocus autocomplete="name"/>
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}"/>
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                         autocomplete="username"/>
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}"/>
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                         autocomplete="new-password"/>
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}"/>
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                         name="password_confirmation" required autocomplete="new-password"/>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required/>

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                   href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
