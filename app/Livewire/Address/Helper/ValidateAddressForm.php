<?php

namespace App\Livewire\Address\Helper;

trait ValidateAddressForm
{

    public function rules(): array
    {
        return [
            'street_number' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
        ];
    }

    public function messages(): array
    {
        return [
            'street_number.required' => __('The street number is required.'),
            'street_number.string' => __('The street number must be a string.'),
            'street_number.max' => __('The street number may not be greater than 255 characters.'),
            'country_id.required' => __('The country is required.'),
            'country_id.exists' => __('The selected country is invalid.'),
            'state_id.required' => __('The state is required.'),
            'state_id.exists' => __('The selected state is invalid.'),
            'city_id.required' => __('The city is required.'),
            'city_id.exists' => __('The selected city is invalid.'),
        ];
    }

}
