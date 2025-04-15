<?php

namespace App\Livewire\Alem\Employee\Profile\Helper;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;

trait ValidatePersonalData
{
    /**
     * Validation rules for the employee personal data
     */
    protected function rules(): array
    {
        return [
            'employee_status' => 'required|string|in:'.implode(',', array_column(EmployeeStatus::cases(), 'value')),
            'personal_number' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[a-zA-Z0-9äöüÄÖÜß\.\-_\/\s]*$/',
            ],
            //            'employment_type' => [
            //                'required',
            //                'string',
            //                'max:50',
            //                'regex:/^[a-zA-Z0-9äöüÄÖÜß\.\-\/\s%\(\)]*$/',
            //            ],

            'supervisor' => 'nullable|exists:users,id',
            'joined_at' => 'required|date|before_or_equal:today',
            'leave_at' => 'nullable|date|after_or_equal:joined_at',
            'probation_enum' => 'required|string|in:'.implode(',', array_column(Probation::cases(), 'value')),
            'probation_at' => 'nullable|date|after_or_equal:joined_at',
            'notice_at' => 'nullable|date|after_or_equal:joined_at',
            'notice_enum' => 'nullable|string|in:'.implode(',', array_column(NoticePeriod::cases(), 'value')),
            'profession' => 'required|exists:professions,id',
            'stage' => 'required|exists:stages,id',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'employee_status.required' => __('Employee status is required.'),
            'employee_status.in' => __('The selected employee status is invalid.'),

            'personal_number.max' => __('Personal number cannot exceed 20 characters.'),
            'personal_number.regex' => __('Personal number may only contain letters, numbers, dots, dashes, underscores, slashes and spaces.'),

            'employment_type.required' => __('Employment type is required.'),
            'employment_type.max' => __('Employment type cannot exceed 50 characters.'),
            'employment_type.regex' => __('Employment type may only contain letters, numbers, dots, dashes, underscores, slashes and spaces.'),

            'supervisor.exists' => __('The selected supervisor does not exist.'),

            'joined_at.required' => __('Joined date is required.'),
            'joined_at.date' => __('Joined date must be a valid date.'),
            'joined_at.before_or_equal' => __('Joined date cannot be in the future.'),

            'leave_at.date' => __('Leave date must be a valid date.'),
            'leave_at.after_or_equal' => __('Leave date must be after or equal to joined date.'),

            'probation_enum.required' => __('Probation period is required.'),
            'probation_enum.in' => __('The selected probation period is invalid.'),

            'probation_at.date' => __('Probation end date must be a valid date.'),
            'probation_at.after_or_equal' => __('Probation end date must be after or equal to joined date.'),

            'notice_at.date' => __('Notice date must be a valid date.'),
            'notice_at.after_or_equal' => __('Notice date must be after or equal to joined date.'),

            'notice_enum.in' => __('The selected notice period is invalid.'),

            'profession.required' => __('Profession is required.'),
            'profession.exists' => __('The selected profession does not exist.'),

            'stage.required' => __('Stage is required.'),
            'stage.exists' => __('The selected stage does not exist.'),
        ];
    }
}
