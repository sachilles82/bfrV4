<?php

namespace App\Livewire\Alem\Employee\Profile\Family\Helper;

use App\Enums\User\Gender;
use Illuminate\Validation\Rule;

trait ValidateCreateChild
{
    /**
     * Validiert required Felder immer und nullable Felder nur wenn gefüllt
     */
    public function validateRequiredAndFilled(): array
    {
        $rules = $this->getValidationRules();

        if (empty($rules)) {
            return [];
        }

        return $this->validate($rules);
    }

    /**
     * Gibt die Validierungsregeln zurück
     * Required Felder werden immer validiert
     * Nullable Felder nur wenn sie gefüllt sind
     */
    private function getValidationRules(): array
    {
        // Required Felder - immer validieren
        $rules = [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇßñÑ\s\-\'\.]+$/u',
                'not_regex:/\d/',
            ],
            'gender' => ['required', Rule::enum(Gender::class)],
            'birthdate' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before:today',
                'after:' . now()->subYears(25)->format('Y-m-d')
            ],
        ];

        // Nullable Felder - nur validieren wenn gefüllt
        if (filled($this->ahv_number)) {
            $rules['ahv_number'] = [
                'string',
                'size:16',
                'regex:/^756\.\d{4}\.\d{4}\.\d{2}$/'
            ];
        }

        return $rules;
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            'name.required' => __('The child\'s name is required.'),
            'name.min' => __('The name must be at least 2 characters.'),
            'name.max' => __('The name must not exceed 100 characters.'),
            'name.regex' => __('The name can only contain letters, spaces, hyphens and apostrophes.'),
            'name.not_regex' => __('The name contains invalid characters.'),

            'gender.required' => __('Please select a gender.'),
            'gender.enum' => __('The selected gender is invalid.'),

            'birthdate.required' => __('The birthdate is required.'),
            'birthdate.date' => __('Please enter a valid date.'),
            'birthdate.before' => __('The birthdate must be in the past.'),
            'birthdate.after' => __('The birthdate cannot be more than 25 years ago.'),

            'ahv_number.size' => __('The AHV number must be exactly 16 characters.'),
            'ahv_number.regex' => __('Invalid AHV number format. Example: 756.1234.5678.90'),
        ];
    }
}
