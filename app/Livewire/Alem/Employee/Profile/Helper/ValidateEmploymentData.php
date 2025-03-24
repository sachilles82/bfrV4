<?php

namespace App\Livewire\Alem\Employee\Profile\Helper;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;

trait ValidateEmploymentData
{
    /**
     * Validation rules for employment data
     */
    protected function rules()
    {
        return [
            'ahv_number' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'nationality' => 'required|string|max:100',
            'hometown' => 'required|string|max:100',
            'religion' => 'required|string|in:' . implode(',', array_map(fn($item) => $item->value, Religion::cases())),
            'civil_status' => 'required|string|in:' . implode(',', array_map(fn($item) => $item->value, CivilStatus::cases())),
            'residence_permit' => 'required|string|in:' . implode(',', array_map(fn($item) => $item->value, Residence::cases())),
            'iban' => 'required|string|max:100',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages()
    {
        return [
            'ahv_number.required' => __('The AHV number is required.'),
            'birthdate.required' => __('The birthdate is required.'),
            'birthdate.date' => __('The birthdate must be a valid date.'),
            'nationality.required' => __('The nationality is required.'),
            'hometown.required' => __('The hometown is required.'),
            'religion.required' => __('The religion is required.'),
            'civil_status.required' => __('The civil status is required.'),
            'residence_permit.required' => __('The residence permit is required.'),
            'iban.required' => __('The IBAN is required.'),
        ];
    }
}
