<?php

namespace App\Livewire\Alem\Employee\Profile\EmploymentData\Helper;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use Illuminate\Validation\Rule;

trait ValidateEmploymentData
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

        // AHV Number
        if ($this->ahvNumberHasChanged()) {
            $rules['ahv_number'] = 'required|string|max:255';
        }

//        // Birthdate
//        if ($this->birthdateHasChanged()) {
//            $rules['birthdate'] = 'required|date|before:today';
//        }

        // Nationality
        if ($this->nationalityHasChanged()) {
            $rules['nationality'] = 'required|string|max:100';
        }

        // Hometown
        if ($this->hometownHasChanged()) {
            $rules['hometown'] = 'required|string|max:100';
        }

        // Religion
        if ($this->religionHasChanged()) {
            $rules['religion'] = ['required', Rule::enum(Religion::class)];
        }

        // Civil Status
        if ($this->civilStatusHasChanged()) {
            $rules['civil_status'] = ['required', Rule::enum(CivilStatus::class)];
        }

        // Residence Permit
        if ($this->residencePermitHasChanged()) {
            $rules['residence_permit'] = ['required', Rule::enum(Residence::class)];
        }

        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        return [
            'ahv_number' => 'required|string|max:255',
//            'birthdate' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'hometown' => 'required|string|max:100',
            'religion' => ['required', Rule::enum(Religion::class)],
            'civil_status' => ['required', Rule::enum(CivilStatus::class)],
            'residence_permit' => ['required', Rule::enum(Residence::class)]
        ];
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            // AHV Number
            'ahv_number.required' => __('The AHV number is required.'),
            'ahv_number.string' => __('The AHV number must be a string.'),
            'ahv_number.max' => __('The AHV number must not exceed 255 characters.'),

//            // Birthdate
//            'birthdate.required' => __('The birthdate is required.'),
//            'birthdate.date' => __('The birthdate must be a valid date.'),
//            'birthdate.before' => __('The birthdate must be in the past.'),

            // Nationality
            'nationality.required' => __('The nationality is required.'),
            'nationality.string' => __('The nationality must be a string.'),
            'nationality.max' => __('The nationality must not exceed 100 characters.'),

            // Hometown
            'hometown.required' => __('The hometown is required.'),
            'hometown.string' => __('The hometown must be a string.'),
            'hometown.max' => __('The hometown must not exceed 100 characters.'),

            // Religion
            'religion.required' => __('The religion is required.'),
            'religion.enum' => __('The selected religion is invalid.'),

            // Civil Status
            'civil_status.required' => __('The civil status is required.'),
            'civil_status.enum' => __('The selected civil status is invalid.'),

            // Residence Permit
            'residence_permit.required' => __('The residence permit is required.'),
            'residence_permit.enum' => __('The selected residence permit is invalid.'),
        ];
    }

    /**
     * Gibt alle geänderten Felder mit ihren Änderungen zurück
     */
    public function getChangedFields(): array
    {
        $changed = [];

        if ($this->ahvNumberHasChanged()) {
            $changed['ahv_number'] = [
                'old' => $this->originalData['ahv_number'] ?? null,
                'new' => $this->ahv_number
            ];
        }

//        if ($this->birthdateHasChanged()) {
//            $changed['birthdate'] = [
//                'old' => $this->originalData['birthdate'] ?? null,
//                'new' => $this->birthdate
//            ];
//        }

        if ($this->nationalityHasChanged()) {
            $changed['nationality'] = [
                'old' => $this->originalData['nationality'] ?? null,
                'new' => $this->nationality
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

        if ($this->civilStatusHasChanged()) {
            $changed['civil_status'] = [
                'old' => $this->originalData['civil_status'] ?? null,
                'new' => $this->civil_status
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
     * Helper: Check ob AHV Number geändert wurde
     */
    private function ahvNumberHasChanged(): bool
    {
        return $this->ahv_number !== ($this->originalData['ahv_number'] ?? '');
    }

//    /**
//     * Helper: Check ob Birthdate geändert wurde
//     */
//    private function birthdateHasChanged(): bool
//    {
//        return $this->birthdate !== ($this->originalData['birthdate'] ?? '');
//    }

    /**
     * Helper: Check ob Nationality geändert wurde
     */
    private function nationalityHasChanged(): bool
    {
        return $this->nationality !== ($this->originalData['nationality'] ?? '');
    }

    /**
     * Helper: Check ob Hometown geändert wurde
     */
    private function hometownHasChanged(): bool
    {
        return $this->hometown !== ($this->originalData['hometown'] ?? '');
    }

    /**
     * Helper: Check ob Religion geändert wurde
     */
    private function religionHasChanged(): bool
    {
        return $this->religion !== ($this->originalData['religion'] ?? '');
    }

    /**
     * Helper: Check ob Civil Status geändert wurde
     */
    private function civilStatusHasChanged(): bool
    {
        return $this->civil_status !== ($this->originalData['civil_status'] ?? '');
    }

    /**
     * Helper: Check ob Residence Permit geändert wurde
     */
    private function residencePermitHasChanged(): bool
    {
        return $this->residence_permit !== ($this->originalData['residence_permit'] ?? '');
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->ahvNumberHasChanged() ||
//            $this->birthdateHasChanged() ||
            $this->nationalityHasChanged() ||
            $this->hometownHasChanged() ||
            $this->religionHasChanged() ||
            $this->civilStatusHasChanged() ||
            $this->residencePermitHasChanged();
    }

    /**
     * Validiere einzelnes Feld on-the-fly (z.B. wire:blur)
     */
    public function validateField(string $fieldName): void
    {
        $fieldMapping = [
            'ahv_number' => 'ahvNumberHasChanged',
//            'birthdate' => 'birthdateHasChanged',
            'nationality' => 'nationalityHasChanged',
            'hometown' => 'hometownHasChanged',
            'religion' => 'religionHasChanged',
            'civil_status' => 'civilStatusHasChanged',
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
