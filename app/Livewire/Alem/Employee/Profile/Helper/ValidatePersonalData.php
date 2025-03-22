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
//            'employee_status' => 'required|string',
//            'personal_number' => 'required|string|max:255',
//            'employment_type' => 'required|string|max:255',
//            'supervisor' => 'required|string|max:255',
//            'date_hired' => 'required|date',
//            'date_fired' => 'nullable|date|after_or_equal:date_hired',
//            'probation' => 'required|date',
//            'probation_enum' => 'required|string',
//            'notice_period' => 'required|date',
//            'notice_period_enum' => 'required|string',
//            'profession' => 'nullable|exists:professions,id',
//            'stage' => 'nullable|exists:stages,id',
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
            'date_hired' => __('hiring date'),
            'date_fired' => __('termination date'),
            'probation' => __('probation date'),
            'probation_enum' => __('probation period'),
            'notice_period' => __('notice period date'),
            'notice_period_enum' => __('notice period duration'),
            'profession' => __('profession'),
            'stage' => __('stage'),
        ];
    }
}
