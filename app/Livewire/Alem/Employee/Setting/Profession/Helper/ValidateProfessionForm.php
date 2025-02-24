<?php

namespace App\Livewire\Alem\Employee\Setting\Profession\Helper;

trait ValidateProfessionForm
{
    public function rules(): array
    {
        return [
            // Für den zugehörigen User (Update: Name als employee_name, E-Mail, Gender)
            'name' => 'required|string|min:3|unique:professions,name,' . $this->professionId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => __('Employee name is required.'),
            'name.min'        => __('Employee name must be at least 3 characters.'),
        ];
    }

}
