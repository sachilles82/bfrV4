@props([
    'label',
    'error' => false,
    'helpText' => false,
    'for',
    'badge' => false,
    'model' => '',
])
<div x-data="{ value: @entangle($model) }" class="col-span-2 mt-0">
    <div>
        <div class="flex items-center">
            <label for="{{ $for}}" class="block text-sm font-medium leading-6 dark:text-white text-gray-900">
                {{ $label}}
            </label>
            @if ($badge)
                <span
                    x-show="!value"
                    x-cloak
                    @class([
                        'ml-2 inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium',
                        // falls "* Required"
                        'bg-red-100 text-red-700 dark:bg-red-400/10 dark:text-red-400' => $badge === __('Required'),
                        // sonst (z.B. "* Optional")
                        'bg-gray-100 text-gray-600 dark:bg-gray-400/10 dark:text-gray-400' => $badge !== __('Required'),
                    ])
                >
                    <span class="mr-1">*</span>
                    {{ $badge }}
                </span>
            @endif
        </div>


        {{--    der Slot ist das input element--}}
        <div class="mt-0">
            {{ $slot }}
        </div>

        @if ($error)
            @error($for)
            <x-pupi.input.error-danger for="{{ $for }}"/>
            @enderror
        @endif

        @if($helpText)
            <p class="mt-2 text-sm text-gray-500" id="">
                {{ $helpText }}
            </p>
        @endif
    </div>
</div>

<!-- Input Group example -->
{{--<x-input.group label="{{ __('Country Code')}} " for="code" :error="$errors->first('code')" help-text="{{ __('Country ISO Code') }}" >--}}
{{--    hier kann jedes input element eingefügt werden, textarea input datepicker etc
    <x-input.text wire:model.live="code" name="code" id="code" placeholder="{{ __('') }}"/>--}}
{{--</x-input.group>--}}
