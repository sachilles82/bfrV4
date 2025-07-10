<?php

namespace App\Livewire\Alem\Employee\Profile\Family\Helper;

use App\Enums\User\Gender;
use Illuminate\Validation\Rule;

trait ValidateChild
{
    /**
     * Validiert nur die geänderten Felder
     */
    public function validateOnlyChanged(): array
    {
        $rules = $this->getChangedFieldRules();

        if (empty($rules)) {
            return [];
        }

        return $this->validate($rules);
    }

    /**
     * Gibt nur die Regeln für geänderte Felder zurück
     */
    private function getChangedFieldRules(): array
    {
        $rules = [];

        // Name
        if ($this->nameHasChanged()) {
            $rules['name'] = [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇ\s\-\'\.]+$/u'
            ];
        }

        // Gender
        if ($this->genderHasChanged()) {
            $rules['gender'] = ['required', Rule::enum(Gender::class)];
        }

        // Birthdate
        if ($this->birthdateHasChanged()) {
            $rules['birthdate'] = [
                'required',
                'date',
                'date_format:Y-m-d',
                'before:today',
                'after:' . now()->subYears(25)->format('Y-m-d')
            ];
        }

        // AHV Number
        if ($this->ahvNumberHasChanged()) {
            $rules['ahv_number'] = [
                'nullable',
                'string',
                'size:16',
                'regex:/^756\.\d{4}\.\d{4}\.\d{2}$/'
            ];
        }

        // Valid Until
        if ($this->validUntilHasChanged()) {
            $rules['valid_until'] = [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after:today'
            ];
        }

        return $rules;
    }

    /**
     * Alle Validierungsregeln
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇ\s\-\'\.]+$/u'
            ],
            'gender' => ['required', Rule::enum(Gender::class)],
            'birthdate' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before:today',
                'after:' . now()->subYears(25)->format('Y-m-d')
            ],
            'ahv_number' => [
                'nullable',
                'string',
                'size:16',
                'regex:/^756\.\d{4}\.\d{4}\.\d{2}$/'
            ],
            'valid_until' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after:today'
            ]
        ];
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

            'gender.required' => __('Please select a gender.'),
            'gender.enum' => __('The selected gender is invalid.'),

            'birthdate.required' => __('The birthdate is required.'),
            'birthdate.before' => __('The birthdate must be in the past.'),
            'birthdate.after' => __('The birthdate cannot be more than 25 years ago.'),

            'ahv_number.size' => __('The AHV number must be exactly 16 characters.'),
            'ahv_number.regex' => __('Invalid AHV number format. Example: 756.1234.5678.90'),

            'valid_until.after' => __('The validity date must be in the future.'),
        ];
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->nameHasChanged() ||
            $this->genderHasChanged() ||
            $this->birthdateHasChanged() ||
            $this->ahvNumberHasChanged() ||
            $this->validUntilHasChanged();
    }

    // Helper-Methoden für Änderungsprüfung
    private function nameHasChanged(): bool
    {
        return $this->name !== ($this->originalData['name'] ?? null);
    }

    private function genderHasChanged(): bool
    {
        return $this->gender !== ($this->originalData['gender'] ?? null);
    }

    private function birthdateHasChanged(): bool
    {
        return $this->birthdate !== ($this->originalData['birthdate'] ?? null);
    }

    private function ahvNumberHasChanged(): bool
    {
        return $this->ahv_number !== ($this->originalData['ahv_number'] ?? null);
    }

    private function validUntilHasChanged(): bool
    {
        return $this->valid_until !== ($this->originalData['valid_until'] ?? null);
    }
}
