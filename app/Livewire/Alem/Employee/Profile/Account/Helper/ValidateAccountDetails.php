<?php

namespace App\Livewire\Alem\Employee\Profile\Account\Helper;

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rule;

trait ValidateAccountDetails
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

        // Gender
        if ($this->genderHasChanged()) {
            $rules['gender'] = ['required', Rule::enum(Gender::class)];
        }

        // Name
        if ($this->nameHasChanged()) {
            $rules['name'] = 'required|string|min:3|max:255';
        }

        // Email
        if ($this->emailHasChanged()) {
            $rules['email'] = [
                'required',
                'email:rfc,dns,spoof',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId)
            ];
        }

        // Phone
        if ($this->phoneHasChanged()) {
            if (!empty($this->phone_1)) {
                $rules['phone_1'] = [
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^[\d\s\-\+\(\)]+$/'
                ];
            }
        }

        // Phone 2 (optional, falls benötigt)
        if ($this->phone2HasChanged()) {
            if (!empty($this->phone_2)) {
                $rules['phone_2'] = [
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^[\d\s\-\+\(\)]+$/'
                ];
            }
        }
        // Model Status
        if ($this->modelStatusHasChanged()) {
            $rules['model_status'] = ['required', Rule::enum(ModelStatus::class)];
        }

        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        return [
            'gender' => ['required', Rule::enum(Gender::class)],
            'name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                'email:rfc,dns,spoof',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId)
            ],
            'phone_1' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/'
            ],
            'phone_2' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/'
            ],
            'model_status' => ['required', Rule::enum(ModelStatus::class)]
        ];
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            // Name
            'name.required' => __('Employee name is required.'),
            'name.string' => __('Employee name must be a string.'),
            'name.min' => __('Employee name must be at least 3 characters.'),
            'name.max' => __('Employee name must not exceed 255 characters.'),

            // Gender
            'gender.required' => __('Gender is required.'),
            'gender.enum' => __('The selected gender is invalid.'),

            // Email
            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be a valid email address.'),
            'email.max' => __('Email must not exceed 255 characters.'),
            'email.unique' => __('This email address is already in use.'),

            // Phone
            'phone_1.string' => __('Phone number must be a string.'),
            'phone_1.max' => __('Phone number must not exceed 20 characters.'),
            'phone_1.regex' => __('Phone number format is invalid.'),

            'phone_2.string' => __('Phone number must be a string.'),
            'phone_2.max' => __('Phone number must not exceed 20 characters.'),
            'phone_2.regex' => __('Phone number format is invalid.'),

            // Model Status
            'model_status.required' => __('Account status is required.'),
            'model_status.enum' => __('The selected account status is invalid.'),
        ];
    }

    /**
     * Gibt alle geänderten Felder mit ihren Änderungen zurück
     * Nützlich für Logging oder Debugging
     */
    public function getChangedFields(): array
    {
        $changed = [];

        if ($this->genderHasChanged()) {
            $changed['gender'] = [
                'old' => $this->originalData['gender'] ?? null,
                'new' => $this->gender
            ];
        }

        if ($this->nameHasChanged()) {
            $changed['name'] = [
                'old' => $this->originalData['name'] ?? null,
                'new' => $this->name
            ];
        }

        if ($this->emailHasChanged()) {
            $changed['email'] = [
                'old' => $this->originalData['email'] ?? null,
                'new' => $this->email
            ];
        }

        if ($this->phoneHasChanged()) {
            $changed['phone_1'] = [
                'old' => $this->originalData['phone_1'] ?? null,
                'new' => $this->phone_1
            ];
        }

        if ($this->phone2HasChanged()) {
            $changed['phone_2'] = [
                'old' => $this->originalData['phone_2'] ?? null,
                'new' => $this->phone_2
            ];
        }

        if ($this->modelStatusHasChanged()) {
            $changed['model_status'] = [
                'old' => $this->originalData['model_status'] ?? null,
                'new' => $this->model_status
            ];
        }

        return $changed;
    }

    /**
     * Helper: Check ob Gender geändert wurde
     */
    private function genderHasChanged(): bool
    {
        return $this->gender !== ($this->originalData['gender'] ?? '');
    }

    /**
     * Helper: Check ob Name geändert wurde
     */
    private function nameHasChanged(): bool
    {
        return $this->name !== ($this->originalData['name'] ?? '');
    }

    /**
     * Helper: Check ob Email geändert wurde
     */
    private function emailHasChanged(): bool
    {
        return $this->email !== ($this->originalData['email'] ?? '');
    }

    /**
     * Helper: Check ob Phone geändert wurde
     */
    private function phoneHasChanged(): bool
    {
        return $this->phone_1 !== ($this->originalData['phone_1'] ?? '');
    }

    /**
     * Helper: Check ob Phone 2 geändert wurde
     */
    private function phone2HasChanged(): bool
    {
        return $this->phone_2 !== ($this->originalData['phone_2'] ?? '');
    }

    /**
     * Helper: Check ob Model Status geändert wurde
     */
    private function modelStatusHasChanged(): bool
    {
        return $this->model_status !== ($this->originalData['model_status'] ?? '');
    }

    /**
     * Generische Methode zum Vergleich von Integer-Arrays
     * Normalisiert beide Arrays (sortiert, unique, nur integers) vor dem Vergleich
     */
    private function intArraysHaveChanged(array $original, array $current): bool
    {
        $normalizedOriginal = $this->normalizeIntArray($original);
        $normalizedCurrent = $this->normalizeIntArray($current);
        return !$this->arraysAreEqual($normalizedOriginal, $normalizedCurrent);
    }

    /**
     * Normalisiere Integer Array (sortiert, unique, nur integers)
     */
    private function normalizeIntArray(array $array): array
    {
        $normalized = array_map('intval', array_filter($array, 'is_numeric'));
        $normalized = array_unique($normalized);
        sort($normalized);
        return array_values($normalized);
    }

    /**
     * Vergleiche zwei Arrays nach Normalisierung
     */
    private function arraysAreEqual(array $array1, array $array2): bool
    {
        return $array1 === $array2;
    }

    /**
     * Validiere einzelnes Feld on-the-fly (z.B. wire:blur)
     */
    public function validateField(string $fieldName): void
    {
        $fieldMapping = [
            'gender' => 'genderHasChanged',
            'name' => 'nameHasChanged',
            'email' => 'emailHasChanged',
            'phone_1' => 'phoneHasChanged',
            'phone_2' => 'phone2HasChanged',
            'model_status' => 'modelStatusHasChanged',
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

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->genderHasChanged() ||
            $this->nameHasChanged() ||
            $this->emailHasChanged() ||
            $this->phoneHasChanged() ||
            $this->phone2HasChanged() ||
            $this->modelStatusHasChanged();
    }
}
