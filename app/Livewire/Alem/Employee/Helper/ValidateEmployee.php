<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rule;

trait ValidateEmployee
{
    public function rules(): array
    {
        return [
            // User fields
            'gender' => ['required', Rule::in(array_column(Gender::cases(), 'value'))],
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => [
                'required', 'email', Rule::unique('users', 'email')->ignore($this->employee->user->id ?? null),
            ],
            'password' => 'required|string|min:8',

            // Role validation - at least one role must be selected
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,id',

            // Team selection
            'selectedTeams' => 'required|array|min:1',
            'selectedTeams.*' => ['exists:teams,id'],

            // Department
            'department' => ['nullable', 'exists:departments,id'],

            // Employee fields
            'profession' => 'nullable|exists:professions,id',
            'stage' => 'nullable|exists:stages,id',

            'employee_status' => ['required', Rule::in(array_column(EmployeeStatus::cases(), 'value'))],
            'joined_at' => 'required|date',

//            'model_status' => ['required', Rule::in(array_column(ModelStatus::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            // User field messages
            'gender.required' => __('Gender is required.'),

            'name.required' => __('Employee name is required.'),
            'name.min' => __('Employee name must be at least 3 characters.'),

            'last_name.required' => __('Employee last name is required.'),
            'last_name.min' => __('Employee last name must be at least 3 characters.'),

            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be valid.'),
            'email.unique' => __('Email already exists.'),

            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 8 characters.'),

            // Role validation messages
            'selectedRoles.required' => __('At least one role must be selected.'),
            'selectedRoles.array' => __('Roles must be provided as a list.'),
            'selectedRoles.min' => __('Please select at least one role.'),
            'selectedRoles.*.exists' => __('Selected role does not exist.'),

            // Team messages
            'selectedTeams.required' => __('At least one team must be selected.'),
            'selectedTeams.array' => __('Teams must be provided as a list.'),
            'selectedTeams.min' => __('Please select at least one team.'),
            'selectedTeams.*.exists' => __('The selected team is invalid.'),

            // Department messages
            'department.exists' => __('The selected department is invalid.'),

            // Employee field messages
            'profession.exists' => __('The selected profession is invalid.'),
            'stage.exists' => __('The selected stage is invalid.'),

            'employee_status.required' => __('Employee status is required.'),
            'joined_at.required' => __('Joined date is required.'),
            'joined_at.date' => __('Joined date must be a valid date.'),
            'model_status.required' => __('Account status is required.'),
        ];
    }
}
