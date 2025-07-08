<?php

namespace App\Livewire\Alem\Employee\Profile\Marital\Helper;

use App\Enums\Employee\CivilStatus;
use Illuminate\Validation\Rule;

/**
 * Trait für die Validierung in der Marital Status Komponente.
 */
trait ValidateMaritalData
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

        // Civil Status
        if ($this->civilStatusHasChanged()) {
            $rules['civil_status'] = ['nullable', Rule::enum(CivilStatus::class)];
        }

        // Name Partner
        if ($this->namePartnerHasChanged()) {
            $rules['name_partner'] = [
                'nullable',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇ\s\-\'\.]+$/u',
                'not_regex:/\d/',
                'not_regex:/[!@#$%^&*()_+=\[\]{};:"\\|,<>\/?]/'
            ];
        }

        // Single Parent
        if ($this->singleParentHasChanged()) {
            $rules['single_parent'] = [
                'nullable',
                'boolean'
            ];
        }

        // Birthdate Partner
        if ($this->birthdatePartnerHasChanged()) {
            $rules['birthdate_partner'] = [
                'nullable',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z0-9äöüÄÖÜéèêëàâîïôùûçÇ\s\.\-\/]+$/u'
            ];
        }

        // AHV Partner
        if ($this->ahvPartnerHasChanged()) {
            $rules['ahv_partner'] = [
                'nullable',
                'string',
                'size:16',
                'regex:/^756\.\d{4}\.\d{4}\.\d{2}$/'
            ];
        }

        // Marriage At
        if ($this->marriageAtHasChanged()) {
            $rules['marriage_at'] = [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:today',
                'after:' . now()->subYears(60)->format('Y-m-d')
            ];
        }

        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        return [
            'civil_status' => ['nullable', Rule::enum(CivilStatus::class)],
            'name_partner' => [
                'nullable',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇ\s\-\'\.]+$/u',
                'not_regex:/\d/',
                'not_regex:/[!@#$%^&*()_+=\[\]{};:"\\|,<>\/?]/'
            ],
            'single_parent' => [
                'nullable',
                'boolean'
            ],
            'birthdate_partner' => [
                'nullable',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z0-9äöüÄÖÜéèêëàâîïôùûçÇ\s\.\-\/]+$/u'
            ],
            'ahv_partner' => [
                'nullable',
                'string',
                'size:16',
                'regex:/^756\.\d{4}\.\d{4}\.\d{2}$/'
            ],
            'marriage_at' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:today',
                'after:' . now()->subYears(60)->format('Y-m-d')
            ],
        ];
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            // Civil Status
            'civil_status.enum' => __('The selected civil status is invalid.'),

            // Name Partner
            'name_partner.string' => __('The partner name must be a string.'),
            'name_partner.min' => __('The partner name must be at least 2 characters.'),
            'name_partner.max' => __('The partner name must not exceed 100 characters.'),
            'name_partner.regex' => __('The partner name can only contain letters, hyphens, apostrophes and dots.'),
            'name_partner.not_regex' => __('The partner name cannot contain numbers or special characters.'),

            // Single Parent
            'single_parent.boolean' => __('The single parent field must be true or false.'),

            // Birthdate Partner
            'birthdate_partner.string' => __('The partner birthdate must be a string.'),
            'birthdate_partner.min' => __('The partner birthdate must be at least 2 characters.'),
            'birthdate_partner.max' => __('The partner birthdate must not exceed 50 characters.'),
            'birthdate_partner.regex' => __('The partner birthdate contains invalid characters.'),

            // AHV Partner
            'ahv_partner.string' => __('The partner AHV number must be a string.'),
            'ahv_partner.size' => __('The partner AHV number must be exactly 16 characters long (13 digits and 3 dots).'),
            'ahv_partner.regex' => __('The partner AHV number format is invalid. Format: 756.1234.5678.90'),

            // Marriage At
            'marriage_at.date' => __('The marriage date must be a valid date.'),
            'marriage_at.date_format' => __('The marriage date must be in format YYYY-MM-DD.'),
            'marriage_at.before_or_equal' => __('The marriage date cannot be in the future.'),
            'marriage_at.after' => __('The marriage date cannot be more than 60 years ago.'),
        ];
    }

    /**
     * Gibt alle geänderten Felder mit ihren Änderungen zurück
     */
    public function getChangedFields(): array
    {
        $changed = [];

        if ($this->civilStatusHasChanged()) {
            $changed['civil_status'] = [
                'old' => $this->originalData['civil_status'] ?? null,
                'new' => $this->civil_status
            ];
        }

        if ($this->namePartnerHasChanged()) {
            $changed['name_partner'] = [
                'old' => $this->originalData['name_partner'] ?? null,
                'new' => $this->name_partner
            ];
        }

        if ($this->singleParentHasChanged()) {
            $changed['single_parent'] = [
                'old' => $this->originalData['single_parent'] ?? null,
                'new' => $this->single_parent
            ];
        }

        if ($this->birthdatePartnerHasChanged()) {
            $changed['birthdate_partner'] = [
                'old' => $this->originalData['birthdate_partner'] ?? null,
                'new' => $this->birthdate_partner
            ];
        }

        if ($this->ahvPartnerHasChanged()) {
            $changed['ahv_partner'] = [
                'old' => $this->originalData['ahv_partner'] ?? null,
                'new' => $this->ahv_partner
            ];
        }

        if ($this->marriageAtHasChanged()) {
            $changed['marriage_at'] = [
                'old' => $this->originalData['marriage_at'] ?? null,
                'new' => $this->marriage_at
            ];
        }

        return $changed;
    }

    /**
     * Helper: Check ob Civil Status geändert wurde
     */
    private function civilStatusHasChanged(): bool
    {
        return $this->civil_status !== ($this->originalData['civil_status'] ?? null);
    }

    /**
     * Helper: Check ob Name Partner geändert wurde
     */
    private function namePartnerHasChanged(): bool
    {
        return $this->name_partner !== ($this->originalData['name_partner'] ?? null);
    }

    /**
     * Helper: Check ob Single Parent geändert wurde
     */
    private function singleParentHasChanged(): bool
    {
        return $this->single_parent !== ($this->originalData['single_parent'] ?? null);
    }

    /**
     * Helper: Check ob Birthdate Partner geändert wurde
     */
    private function birthdatePartnerHasChanged(): bool
    {
        return $this->birthdate_partner !== ($this->originalData['birthdate_partner'] ?? null);
    }

    /**
     * Helper: Check ob AHV Partner geändert wurde
     */
    private function ahvPartnerHasChanged(): bool
    {
        return $this->ahv_partner !== ($this->originalData['ahv_partner'] ?? null);
    }

    /**
     * Helper: Check ob Marriage At geändert wurde
     */
    private function marriageAtHasChanged(): bool
    {
        return $this->marriage_at !== ($this->originalData['marriage_at'] ?? null);
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->civilStatusHasChanged() ||
            $this->namePartnerHasChanged() ||
            $this->singleParentHasChanged() ||
            $this->birthdatePartnerHasChanged() ||
            $this->ahvPartnerHasChanged() ||
            $this->marriageAtHasChanged();
    }

    /**
     * Validiere einzelnes Feld on-the-fly (z.B. wire:blur)
     */
    public function validateField(string $fieldName): void
    {
        $fieldMapping = [
            'civil_status' => 'civilStatusHasChanged',
            'name_partner' => 'namePartnerHasChanged',
            'single_parent' => 'singleParentHasChanged',
            'birthdate_partner' => 'birthdatePartnerHasChanged',
            'ahv_partner' => 'ahvPartnerHasChanged',
            'marriage_at' => 'marriageAtHasChanged',
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
