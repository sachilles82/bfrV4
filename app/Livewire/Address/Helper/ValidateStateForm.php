<?php

namespace App\Livewire\Address\Helper;

trait ValidateStateForm
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'selectedCountry' => 'required|exists:countries,id',
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => __('The name is required.'),
            'name.string' => __('The name must be a string.'),
            'name.max' => __('The name may not be greater than 255 characters.'),
            'selectedCountry.required' => __('The country is required.'),
            'selectedCountry.exists' => __('The selected country is invalid.'),
        ];
    }
}
