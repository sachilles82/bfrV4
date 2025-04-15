<?php

namespace App\Livewire\Alem\Employee\Setting\Profession\Helper;

trait ValidateProfessionForm
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:50'.$this->professionId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('Profession name is required.'),
            'name.min' => __('Profession name must be at least 3 characters.'),
            'name.max' => __('Profession name may not be greater than 50 characters.'),
        ];
    }
}
