<?php

namespace App\Livewire\Account\Employee\Helper;

use Illuminate\Validation\Rule;

trait ValidateEmployee
{
    public function rules(): array
    {
        return [
            // Für den zugehörigen User (Update: Name als employee_name, E-Mail, Gender)
            'name'   => 'required|string|min:3',
            'email'           => [
                'required',
                'email',
                // Ignoriere die aktuelle E-Mail des Users beim Unique-Check
                Rule::unique('users', 'email')->ignore($this->employee->user->id ?? null),
            ],
            'gender'          => 'required|string',

            // Für die Employee-Daten
            'date_hired'      => 'required|date',
            'date_fired'      => 'nullable|date',
            'probation'       => 'nullable|date',
            'social_number'   => 'nullable|string',
            'personal_number' => 'nullable|string',
            'profession'      => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => __('Employee name is required.'),
            'name.min'        => __('Employee name must be at least 3 characters.'),
            'email.required'           => __('Email is required.'),
            'email.email'              => __('Email must be valid.'),
            'email.unique'             => __('Email already exists.'),
            'gender.required'          => __('Gender is required.'),
            'date_hired.required'      => __('Date hired is required.'),
            'date_hired.date'          => __('Date hired must be a valid date.'),
            'date_fired.date'          => __('Date fired must be a valid date.'),
            'probation.date'           => __('Probation must be a valid date.'),
            'social_number.string'     => __('Social number must be a string.'),
            'personal_number.string'   => __('Personal number must be a string.'),
            'profession.string'        => __('Profession must be a string.'),

            // Weitere Meldungen nach Bedarf...
        ];
    }
}
