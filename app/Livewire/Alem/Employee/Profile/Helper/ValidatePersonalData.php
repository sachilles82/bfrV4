<?php

namespace App\Livewire\Alem\Employee\Profile\Helper;

trait ValidatePersonalData
{
    /**
     * Validation rules for the employee personal data
     */
    protected function rules(): array
    {
        return [
            'employee_status' => 'required|string',
            'personal_number' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'supervisor' => 'required|string|max:255',
            'joined_at' => 'required|date',
            'leave_at' => 'nullable|date|after_or_equal:joined_at',
            'probation_at' => 'required|date',
            'probation_enum' => 'required|string',
            'notice_at' => 'required|date',
            'notice_enum' => 'required|string',
            'profession' => 'nullable|exists:professions,id',
            'stage' => 'nullable|exists:stages,id',
        ];
    }

    /**
     * Custom validation attribute names
     */
    protected function validationAttributes(): array
    {
        return [
            'employee_status' => __('employee status'),
            'personal_number' => __('personal number'),
            'employment_type' => __('employment type'),
            'supervisor' => __('supervisor'),
            'joined_at' => __('hiring date'),
            'leave_at' => __('termination date'),
            'probation_at' => __('probation date'),
            'probation_enum' => __('probation period'),
            'notice_at' => __('notice period date'),
            'notice_enum' => __('notice period duration'),
            'profession' => __('profession'),
            'stage' => __('stage'),
        ];
    }
}
