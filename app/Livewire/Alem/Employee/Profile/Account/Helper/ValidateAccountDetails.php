<?php

namespace App\Livewire\Alem\Employee\Profile\Account\Helper;

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rule;

/**
 * Trait für die Validierung in der Account Details Komponente.
 */
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

        // Name - Starke Validierung
        if ($this->nameHasChanged()) {
            $rules['name'] = [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇßñÑ\s\-\'\.]+$/u',
                'not_regex:/\d/',
                'not_regex:/[!@#$%^&*()_+=\[\]{};:"\\|,<>\/?]/',
                'not_regex:/\s{2,}/', // Keine mehrfachen Leerzeichen
            ];
        }

        // Email - Erweiterte Validierung
        if ($this->emailHasChanged()) {
            $rules['email'] = [
                'required',
                'string',
                'email:rfc,dns,spoof,filter',
                'max:255',
                'lowercase',
                Rule::unique('users', 'email')->ignore($this->userId),
                'not_regex:/\.{2,}/', // Keine mehrfachen Punkte
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ];
        }

        // Phone 1 - Starke Validierung
        if ($this->phoneHasChanged()) {
            $rules['phone_1'] = [
                'nullable',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\d\s\-\+\(\)\.]+$/',
                'not_regex:/[a-zA-Z]/', // Keine Buchstaben
                function ($attribute, $value, $fail) {
                    // Entferne alle Nicht-Ziffern für die Längenprüfung
                    $digitsOnly = preg_replace('/\D/', '', $value);
                    if ($digitsOnly && (strlen($digitsOnly) < 10 || strlen($digitsOnly) > 15)) {
                        $fail(__('The phone number must contain between 10 and 15 digits.'));
                    }
                },
            ];
        }

        // Phone 2 - Gleiche Validierung wie Phone 1
        if ($this->phone2HasChanged()) {
            $rules['phone_2'] = [
                'nullable',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\d\s\-\+\(\)\.]+$/',
                'not_regex:/[a-zA-Z]/',
                'different:phone_1', // Muss unterschiedlich zu Phone 1 sein
                function ($attribute, $value, $fail) {
                    $digitsOnly = preg_replace('/\D/', '', $value);
                    if ($digitsOnly && (strlen($digitsOnly) < 10 || strlen($digitsOnly) > 15)) {
                        $fail(__('The phone number must contain between 10 and 15 digits.'));
                    }
                },
            ];
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
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇßñÑ\s\-\'\.]+$/u',
                'not_regex:/\d/',
                'not_regex:/[!@#$%^&*()_+=\[\]{};:"\\|,<>\/?]/',
                'not_regex:/\s{2,}/',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns,spoof,filter',
                'max:255',
                'lowercase',
                Rule::unique('users', 'email')->ignore($this->userId),
                'not_regex:/\.{2,}/',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'phone_1' => [
                'nullable',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\d\s\-\+\(\)\.]+$/',
                'not_regex:/[a-zA-Z]/',
            ],
            'phone_2' => [
                'nullable',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\d\s\-\+\(\)\.]+$/',
                'not_regex:/[a-zA-Z]/',
                'different:phone_1',
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
            'name.min' => __('Employee name must be at least 2 characters.'),
            'name.max' => __('Employee name must not exceed 255 characters.'),
            'name.regex' => __('Employee name can only contain letters, spaces, hyphens, apostrophes and dots.'),
            'name.not_regex' => __('Employee name contains invalid characters or formatting.'),

            // Gender
            'gender.required' => __('Gender is required.'),
            'gender.enum' => __('The selected gender is invalid.'),

            // Email
            'email.required' => __('Email address is required.'),
            'email.string' => __('Email must be a string.'),
            'email.email' => __('Please provide a valid email address.'),
            'email.max' => __('Email address must not exceed 255 characters.'),
            'email.unique' => __('This email address is already registered in the system.'),
            'email.lowercase' => __('Email address must be in lowercase.'),
            'email.regex' => __('Email address format is invalid.'),
            'email.not_regex' => __('Email address contains invalid formatting.'),

            // Phone 1
            'phone_1.string' => __('Phone number must be a string.'),
            'phone_1.min' => __('Phone number must be at least 10 characters.'),
            'phone_1.max' => __('Phone number must not exceed 20 characters.'),
            'phone_1.regex' => __('Phone number can only contain digits, spaces, hyphens, parentheses, dots and plus sign.'),
            'phone_1.not_regex' => __('Phone number cannot contain letters.'),

            // Phone 2
            'phone_2.string' => __('Secondary phone number must be a string.'),
            'phone_2.min' => __('Secondary phone number must be at least 10 characters.'),
            'phone_2.max' => __('Secondary phone number must not exceed 20 characters.'),
            'phone_2.regex' => __('Secondary phone number can only contain digits, spaces, hyphens, parentheses, dots and plus sign.'),
            'phone_2.not_regex' => __('Secondary phone number cannot contain letters.'),
            'phone_2.different' => __('Secondary phone number must be different from primary phone number.'),

            // Model Status
            'model_status.required' => __('Account status is required.'),
            'model_status.enum' => __('The selected account status is invalid.'),
        ];
    }

    /**
     * Gibt alle geänderten Felder mit ihren Änderungen zurück
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
        return $this->gender !== ($this->originalData['gender'] ?? null);
    }

    /**
     * Helper: Check ob Name geändert wurde
     */
    private function nameHasChanged(): bool
    {
        return $this->name !== ($this->originalData['name'] ?? null);
    }

    /**
     * Helper: Check ob Email geändert wurde
     */
    private function emailHasChanged(): bool
    {
        return $this->email !== ($this->originalData['email'] ?? null);
    }

    /**
     * Helper: Check ob Phone geändert wurde
     */
    private function phoneHasChanged(): bool
    {
        return $this->phone_1 !== ($this->originalData['phone_1'] ?? null);
    }

    /**
     * Helper: Check ob Phone 2 geändert wurde
     */
    private function phone2HasChanged(): bool
    {
        return $this->phone_2 !== ($this->originalData['phone_2'] ?? null);
    }

    /**
     * Helper: Check ob Model Status geändert wurde
     */
    private function modelStatusHasChanged(): bool
    {
        return $this->model_status !== ($this->originalData['model_status'] ?? null);
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

    /**
     * Sanitize phone number before saving
     */
    public function sanitizePhoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Entferne mehrfache Leerzeichen und trimme
        return preg_replace('/\s+/', ' ', trim($phone));
    }

    /**
     * Sanitize name before saving
     */
    public function sanitizeName(?string $name): ?string
    {
        if (!$name) {
            return null;
        }

        // Entferne mehrfache Leerzeichen und trimme
        return preg_replace('/\s+/', ' ', trim($name));
    }
}
