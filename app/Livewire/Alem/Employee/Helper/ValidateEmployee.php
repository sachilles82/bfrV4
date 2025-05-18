<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Models\Alem\Department;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait ValidateEmployee
{
    public function rules(): array
    {
        return [
            // User fields
            'gender' => ['required', Rule::enum(Gender::class)],
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => [
                'required', 'email', Rule::unique('users', 'email')->ignore($this->userId ?? null),
            ],

            // Role validation - at least one role must be selected
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,id',

            // Team selection
            'selectedTeams' => 'required|array|min:1',
            'selectedTeams.*' => ['exists:teams,id'],

            'department' => [
                'required',
                'exists:departments,id',
                function ($attribute, $value, $fail) {
                    $departmentExistsInTeam = Department::where('id', $value)
                        ->whereIn('team_id', $this->selectedTeams)
                        ->exists();
                    if (!$departmentExistsInTeam) {
                        $fail(__('The selected department must belong to one of the selected teams.'));
                    }
                },
            ],

            // --- Supervisor Regeln ---
            'supervisor' => [
                'required', 'integer', 'exists:users,id',

                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value == $this->userId) {
                        $fail(__('You cannot be your own supervisor.'));
                    }
                },
            ],

            // Employee fields
            'profession' => 'required|exists:professions,id',
            'stage' => 'required|exists:stages,id',

            'employee_status' => ['required', Rule::enum(EmployeeStatus::class)],
            'joined_at' => 'required|date|before_or_equal:today',

            'model_status' => ['required', Rule::enum(ModelStatus::class),
            ],

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
            'department.required' => __('Department is required.'),
            'department.exists' => __('The selected department is invalid.'),

            // Supervisor messages
            'supervisor.required' => __('Supervisor is required.'),
            'supervisor.exists' => __('The selected supervisor is invalid.'),


            // Employee field messages
            'profession.required' => __('Profession is required.'),
            'profession.exists' => __('The selected profession is invalid.'),
            'stage.required' => __('Stage is required.'),
            'stage.exists' => __('The selected stage is invalid.'),
            'employee_status.required' => __('Employee status is required.'),
            'joined_at.required' => __('Joined date is required.'),
            'joined_at.date' => __('Joined date must be a valid date.'),
            // Model status messages
            'model_status.required' => __('Account status is required.'),
            'model_status.string' => __('Account status must be a string.'),
            'model_status.enum' => __('The Status need to be a valid value from the ModelStatus enum.'),
        ];
    }
}
