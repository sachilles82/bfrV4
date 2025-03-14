<?php

namespace App\Livewire\Alem\Employee\Helper;

use Illuminate\Validation\Rule;

trait ValidateEmployee
{
    public function rules(): array
    {
        return [
            // User fields
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => [
                'required', 'email', Rule::unique('users', 'email')->ignore($this->employee->user->id ?? null),
            ],
            'password' => 'required|string|min:8',
            'gender' => 'required|string',
//            'role' => 'required|string|exists:roles,name',
            'model_status' => 'nullable|string',

            // Team selection
            'selectedTeams' => ['sometimes', 'array'],
            'selectedTeams.*' => ['exists:teams,id'],

            // Employee fields
            'joined_at' => 'required|date',
            'employee_status' => 'required|string',
//            'profession' => 'required|exists:professions,id',
//            'stage' => 'required|exists:stages,id',
        ];
    }

    public function messages(): array
    {
        return [
            // User field messages
            'name.required' => __('Employee name is required.'),
            'name.min' => __('Employee name must be at least 3 characters.'),
            'last_name.required' => __('Employee last name is required.'),
            'last_name.min' => __('Employee last name must be at least 3 characters.'),
            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be valid.'),
            'email.unique' => __('Email already exists.'),
            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 8 characters.'),
            'gender.required' => __('Gender is required.'),
            'role.required' => __('Role is required.'),
            'role.exists' => __('The selected role is invalid.'),

            // Team messages
            'selectedTeams.*.exists' => __('The selected team is invalid.'),

            // Employee field messages
            'joined_at.required' => __('Joined date is required.'),
            'joined_at.date' => __('Joined date must be a valid date.'),
            'employee_status.required' => __('Employee status is required.'),
            'profession.required' => __('Profession is required.'),
            'profession.exists' => __('The selected profession is invalid.'),
            'stage.required' => __('Stage is required.'),
            'stage.exists' => __('The selected stage is invalid.'),
        ];
    }
}
