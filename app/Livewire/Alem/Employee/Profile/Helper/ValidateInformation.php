<?php

namespace App\Livewire\Alem\Employee\Profile\Helper;

use App\Enums\Model\ModelStatus;
use Illuminate\Validation\Rule;

trait ValidateInformation
{
    /**
     * Validierungsregeln für das Update-Formular.
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'gender' => 'required|string',
            'phone_1' => [
                'nullable',
                'string',
                'regex:/^\+?[0-9]{1,4}[-\s]?(?:[0-9]{2,}[-\s]?){1,}[0-9]{2,}$/',
                'min:8',
                'max:20',
            ],
            'selectedTeams' => 'required|array|min:1',
            'selectedTeams.*' => 'exists:teams,id',
            'role' => 'nullable|exists:roles,id',
            'model_status' => 'required|string|in:'.implode(',', array_column(ModelStatus::cases(), 'value')),
        ];
    }

    /**
     * Benutzerdefinierte Validierungsnachrichten
     */
    protected function messages(): array
    {
        return [
            'gender.required' => __('Gender is required.'),
            'name.required' => __('Employee name is required.'),
            'name.min' => __('Employee name must be at least 3 characters.'),
            'last_name.required' => __('Employee last name is required.'),
            'last_name.min' => __('Employee last name must be at least 3 characters.'),
            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be valid.'),
            'email.unique' => __('Email already exists.'),
            'selectedTeams.required' => __('Mindestens ein Team muss ausgewählt werden.'),
            'selectedTeams.min' => __('Mindestens ein Team muss ausgewählt werden.'),
            'selectedTeams.*.exists' => __('Ein ausgewähltes Team existiert nicht.'),
            'role.required' => __('Eine Rolle muss ausgewählt werden.'),
            'role.exists' => __('Die ausgewählte Rolle existiert nicht.'),
            'model_status.required' => __('Account status is required.'),
            'model_status.in' => __('The selected account status is invalid.'),
            'phone_1.regex' => __('Phone number format is invalid. Please enter a valid international or national phone number.'),
            'phone_1.min' => __('Phone number must be at least 8 characters.'),
            'phone_1.max' => __('Phone number cannot exceed 20 characters.'),
        ];
    }
}
