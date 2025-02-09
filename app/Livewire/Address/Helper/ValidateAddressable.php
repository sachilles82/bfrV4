<?php

namespace App\Livewire\Address\Helper;

trait ValidateAddressable
{
    public function rules(): array
    {
        return [
            'street_number'      => 'required|string|max:255',
            'selectedCountry'    => 'required|exists:countries,id',
            'selectedState'      => 'required|exists:states,id',
            'selectedCity'       => 'required|exists:cities,id',
        ];
    }

    public function messages(): array
    {
        return [
            'street_number.required'   => __('The street number is required.'),
            'street_number.string'     => __('The street number must be a string.'),
            'street_number.max'        => __('The street number may not be greater than 255 characters.'),
            'selectedCountry.required' => __('The country is required.'),
            'selectedCountry.exists'   => __('The selected country is invalid.'),
            'selectedState.required'   => __('The state is required.'),
            'selectedState.exists'     => __('The selected state is invalid.'),
            'selectedCity.required'    => __('The city is required.'),
            'selectedCity.exists'      => __('The selected city is invalid.'),
        ];
    }
}
