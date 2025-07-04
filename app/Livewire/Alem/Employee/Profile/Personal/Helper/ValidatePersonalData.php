<?php

namespace App\Livewire\Alem\Employee\Profile\Personal\Helper;

use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

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
            $rules['birthdate'] = 'nullable|date|before:today';
        }

        // AHV Number
        if ($this->ahvNumberHasChanged()) {
            $rules['ahv_number'] = [
                'nullable',
                'string',
                'regex:/^756\.\d{4}\.\d{4}\.\d{2}$/'
            ];
        }

        // Nationality
        if ($this->nationalityHasChanged()) {
            $rules['nationality'] = [
                'nullable',
                'string',
                'max:3',
                Rule::in(array_column($this->countries ?? [], 'code'))
            ];
        }

        // Hometown
        if ($this->hometownHasChanged()) {
            $rules['hometown'] = 'nullable|string|max:255';
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
            'birthdate' => 'nullable|date|before:today',
            'ahv_number' => [
                'nullable',
                'string',
                'regex:/^756\.\d{4}\.\d{4}\.\d{2}$/'
            ],
            'nationality' => [
                'nullable',
                'string',
                'max:3',
                Rule::in(array_column($this->countries ?? [], 'code'))
            ],
            'hometown' => 'nullable|string|max:255',
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
            'birthdate.before' => __('The birthdate must be in the past.'),

            // AHV Number
            'ahv_number.string' => __('The AHV number must be a string.'),
            'ahv_number.regex' => __('The AHV number format is invalid. Format: 756.1234.5678.90'),

            // Nationality
            'nationality.string' => __('The nationality must be a string.'),
            'nationality.max' => __('The nationality code must not exceed 3 characters.'),
            'nationality.in' => __('The selected nationality is invalid.'),

            // Hometown
            'hometown.string' => __('The hometown must be a string.'),
            'hometown.max' => __('The hometown must not exceed 255 characters.'),

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
        // Normalisiere beide Werte für den Vergleich
        $originalBirthdate = $this->normalizeDate($this->originalData['birthdate'] ?? null);
        $currentBirthdate = $this->normalizeDate($this->birthdate);

        return $originalBirthdate !== $currentBirthdate;
    }

    /**
     * Normalisiert ein Datum für den Vergleich
     */
    private function normalizeDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        // Wenn es ein Carbon/DateTime Objekt ist
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }

        // Wenn es ein String ist, parse und formatiere es
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper: Check ob AHV Number geändert wurde
     */
    private function ahvNumberHasChanged(): bool
    {
        return $this->ahv_number !== ($this->originalData['ahv_number'] ?? '');
    }

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
            $this->nationalityHasChanged() ||
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
            'nationality' => 'nationalityHasChanged',
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
