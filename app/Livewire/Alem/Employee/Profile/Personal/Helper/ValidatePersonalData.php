<?php

namespace App\Livewire\Alem\Employee\Profile\Personal\Helper;

use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use Illuminate\Validation\Rule;

/**
 * Trait für die Validierung in der PersonalData Komponente.
 */
trait ValidatePersonalData
{
    /**
     * Validiert nur die geänderten Felder
     * @return array Die validierten Daten
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

        // Birthdate
        if ($this->birthdateHasChanged()) {
            $rules['birthdate'] = [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'before:today',
                'after:' . now()->subYears(90)->format('Y-m-d'),
                'before:' . now()->subYears(14)->format('Y-m-d')
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

        // Country ID
        if ($this->countryIdHasChanged()) {
            $rules['country_id'] = [
                'nullable',
                'integer',
                Rule::in($this->countries->pluck('id')->toArray())
            ];
        }

        // Hometown
        if ($this->hometownHasChanged()) {
            $rules['hometown'] = [
                'nullable',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇ\s\-\'\.]+$/u',
                'not_regex:/\d/',
                'not_regex:/[!@#$%^&*()_+=\[\]{};:"\\|,<>\/?]/'
            ];
        }

        // Religion
        if ($this->religionHasChanged()) {
            $rules['religion'] = ['nullable', Rule::enum(Religion::class)];
        }

        // Residence Permit
        if ($this->residencePermitHasChanged()) {
            $rules['residence_permit'] = ['nullable', Rule::enum(Residence::class)];
        }

        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        return [
            'birthdate' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'before:today',
                'after:' . now()->subYears(90)->format('Y-m-d'),
                'before:' . now()->subYears(14)->format('Y-m-d')
            ],
            'ahv_number' => [
                'nullable',
                'string',
                'size:16',
                'regex:/^756\.\d{4}\.\d{4}\.\d{2}$/'
            ],
            'country_id' => [
                'nullable',
                'integer',
                Rule::in($this->countries->pluck('id')->toArray())
            ],
            'hometown' => [
                'nullable',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇ\s\-\'\.]+$/u',
                'not_regex:/\d/',
                'not_regex:/[!@#$%^&*()_+=\[\]{};:"\\|,<>\/?]/'
            ],
            'religion' => ['nullable', Rule::enum(Religion::class)],
            'residence_permit' => ['nullable', Rule::enum(Residence::class)]
        ];
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            // Birthdate
            'birthdate.date' => __('The birthdate must be a valid date.'),
            'birthdate.date_format' => __('The birthdate must be in format YYYY-MM-DD.'),
            'birthdate.before' => __('The birthdate must be at least 14 years ago.'),
            'birthdate.after' => __('The birthdate cannot be more than 90 years ago.'),

            // AHV Number
            'ahv_number.string' => __('The AHV number must be a string.'),
            'ahv_number.size' => __('The AHV number must be exactly 16 characters long (13 digits and 3 dots).'),
            'ahv_number.regex' => __('The AHV number format is invalid. Format: 756.1234.5678.90'),

            // Country ID
            'country_id.integer' => __('The country must be a valid selection.'),
            'country_id.in' => __('The selected country is invalid.'),

            // Hometown
            'hometown.string' => __('The hometown must be a string.'),
            'hometown.min' => __('The hometown must be at least 2 characters.'),
            'hometown.max' => __('The hometown must not exceed 100 characters.'),
            'hometown.regex' => __('The hometown can only contain letters, hyphens, apostrophes and dots.'),
            'hometown.not_regex' => __('The hometown cannot contain numbers or special characters.'),

            // Religion
            'religion.enum' => __('The selected religion is invalid.'),

            // Residence Permit
            'residence_permit.enum' => __('The selected residence permit is invalid.'),
        ];
    }

    /**
     * Gibt alle geänderten Felder mit ihren Änderungen zurück
     */
    public function getChangedFields(): array
    {
        $changed = [];

        if ($this->birthdateHasChanged()) {
            $changed['birthdate'] = [
                'old' => $this->originalData['birthdate'] ?? null,
                'new' => $this->birthdate
            ];
        }

        if ($this->ahvNumberHasChanged()) {
            $changed['ahv_number'] = [
                'old' => $this->originalData['ahv_number'] ?? null,
                'new' => $this->ahv_number
            ];
        }

        if ($this->countryIdHasChanged()) {
            $changed['country_id'] = [
                'old' => $this->originalData['country_id'] ?? null,
                'new' => $this->country_id
            ];
        }

        if ($this->hometownHasChanged()) {
            $changed['hometown'] = [
                'old' => $this->originalData['hometown'] ?? null,
                'new' => $this->hometown
            ];
        }

        if ($this->religionHasChanged()) {
            $changed['religion'] = [
                'old' => $this->originalData['religion'] ?? null,
                'new' => $this->religion
            ];
        }

        if ($this->residencePermitHasChanged()) {
            $changed['residence_permit'] = [
                'old' => $this->originalData['residence_permit'] ?? null,
                'new' => $this->residence_permit
            ];
        }

        return $changed;
    }

    /**
     * Helper: Check ob Birthdate geändert wurde
     */
    private function birthdateHasChanged(): bool
    {
        return $this->birthdate !== ($this->originalData['birthdate'] ?? null);
    }

    /**
     * Helper: Check ob AHV Number geändert wurde
     */
    private function ahvNumberHasChanged(): bool
    {
        return $this->ahv_number !== ($this->originalData['ahv_number'] ?? null);
    }

    /**
     * Helper: Check ob country_id geändert wurde
     */
    private function countryIdHasChanged(): bool
    {
        return $this->country_id !== ($this->originalData['country_id'] ?? null);
    }

    /**
     * Helper: Check ob Hometown geändert wurde
     */
    private function hometownHasChanged(): bool
    {
        return $this->hometown !== ($this->originalData['hometown'] ?? null);
    }

    /**
     * Helper: Check ob Religion geändert wurde
     */
    private function religionHasChanged(): bool
    {
        return $this->religion !== ($this->originalData['religion'] ?? null);
    }

    /**
     * Helper: Check ob Residence Permit geändert wurde
     */
    private function residencePermitHasChanged(): bool
    {
        return $this->residence_permit !== ($this->originalData['residence_permit'] ?? null);
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->birthdateHasChanged() ||
            $this->ahvNumberHasChanged() ||
            $this->countryIdHasChanged() ||
            $this->hometownHasChanged() ||
            $this->religionHasChanged() ||
            $this->residencePermitHasChanged();
    }

    /**
     * Validiere einzelnes Feld on-the-fly (z.B. wire:blur)
     */
    public function validateField(string $fieldName): void
    {
        $fieldMapping = [
            'birthdate' => 'birthdateHasChanged',
            'ahv_number' => 'ahvNumberHasChanged',
            'country_id' => 'countryIdHasChanged',
            'hometown' => 'hometownHasChanged',
            'religion' => 'religionHasChanged',
            'residence_permit' => 'residencePermitHasChanged',
        ];

        // Prüfe ob das Feld geändert wurde
        if (isset($fieldMapping[$fieldName])) {
            $method = $fieldMapping[$fieldName];
            if (!$this->$method()) {
                // Feld nicht geändert - keine Validierung
                return;
            }
        }

        // Hole nur die Regel für dieses Feld
        $rules = $this->getChangedFieldRules();
        if (isset($rules[$fieldName])) {
            $this->validateOnly($fieldName, [$fieldName => $rules[$fieldName]]);
        }
    }
}
