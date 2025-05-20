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
            // User-Felder
            'gender' => ['required', Rule::enum(Gender::class)],
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId ?? null)],
            'model_status' => ['required', Rule::enum(ModelStatus::class)],
            'joined_at' => 'required|date|before_or_equal:today',

            'department' => ['required', 'exists:departments,id',
                function ($attribute, $value, $fail) {
                    if (!empty($this->selectedTeams)) {
                        $departmentExistsInTeam = Department::where('id', $value)
                            ->whereIn('team_id', $this->selectedTeams)
                            ->exists();
                        if (!$departmentExistsInTeam) {
                            $fail(__('The selected department must belong to one of the selected teams.'));
                        }
                    } elseif (Department::where('id', $value)->doesntExist()) {
                        $fail(__('The selected department is invalid.'));
                    }
                },
            ],

            'selectedTeams' => 'required|array|min:1',
            'selectedTeams.*' => ['exists:teams,id'],

            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,id',

            // Mitarbeiter-Felder
            'employee_status' => ['required', Rule::enum(EmployeeStatus::class)],
            'profession' => 'required|exists:professions,id',
            'stage' => 'required|exists:stages,id',

            'supervisor' => ['required', 'integer', 'exists:users,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (isset($this->userId) && $value == $this->userId) {
                        $fail(__('You cannot be your own supervisor.'));
                    }
                },
            ],

            'invitations' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // User field messages
            'gender.required' => __('Gender is required.'),
            'gender.enum' => __('The selected gender is invalid.'),

            'name.required' => __('Employee name is required.'),
            'name.string' => __('Employee name must be a string.'),
            'name.min' => __('Employee name must be at least 3 characters.'),

            'last_name.required' => __('Employee last name is required.'),
            'last_name.string' => __('Employee last name must be a string.'),
            'last_name.min' => __('Employee last name must be at least 3 characters.'),

            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be a valid email address.'),
            'email.unique' => __('This email address is already in use.'),

            'model_status.required' => __('Account status is required.'),
            'model_status.enum' => __('The selected account status is invalid.'),

            'joined_at.required' => __('Joined date is required.'),
            'joined_at.date' => __('Joined date must be a valid date.'),
            'joined_at.before_or_equal' => __('Joined date cannot be in the future.'),

            'department.required' => __('Department is required.'),
            'department.exists' => __('The selected department is invalid.'),

            'selectedTeams.required' => __('At least one team must be selected.'),
            'selectedTeams.array' => __('Teams must be provided as a list.'),
            'selectedTeams.min' => __('Please select at least one team.'),
            'selectedTeams.*.exists' => __('One of the selected teams is invalid.'),

            'selectedRoles.required' => __('At least one role must be selected.'),
            'selectedRoles.array' => __('Roles must be provided as a list.'),
            'selectedRoles.min' => __('Please select at least one role.'),
            'selectedRoles.*.exists' => __('One of the selected roles is invalid.'),

            // Employee field messages
            'employee_status.required' => __('Employee status is required.'),
            'employee_status.enum' => __('The selected employee status is invalid.'),

            'profession.required' => __('Profession is required.'),
            'profession.exists' => __('The selected profession is invalid.'),

            'stage.required' => __('Stage is required.'),
            'stage.exists' => __('The selected stage is invalid.'),

            'supervisor.required' => __('Supervisor is required.'),
            'supervisor.integer' => __('Supervisor ID must be an integer.'),
            'supervisor.exists' => __('The selected supervisor is invalid.'),

            'invitations.boolean' => __('The invitation setting, if provided, must be true or false.'),
        ];
    }
}
