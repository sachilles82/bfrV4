<?php

namespace App\Livewire\Address\Helper;

trait ValidateCityForm
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => __('The name is required.'),
            'name.string' => __('The name must be a string.'),
            'name.max' => __('The name may not be greater than 255 characters.'),
            'country_id.required' => __('The country is required.'),
            'country_id.exists' => __('The selected country is invalid.'),
            'state_id.required' => __('The State is required.'),
            'state_id.exists' => __('The selected State is invalid.'),
        ];
    }

}
