<?php

namespace App\Livewire\Alem\Employee\Profile\Sos\Helper;

use App\Enums\User\Gender;
use Illuminate\Validation\Rule;

trait ValidateContact
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
     * @return array Die Regeln für die geänderten Felder
     */
    private function getChangedFieldRules(): array
    {
        $rules = [];

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

        // Gender
        if ($this->genderHasChanged()) {
            $rules['gender'] = ['nullable', Rule::enum(Gender::class)];
        }

        if ($this->relatedHasChanged()) {
            $rules['related'] = [
                'nullable',
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
            $emailRule = [
                'nullable',
                'string',
                'email:rfc,dns,spoof,filter',
                'max:255',
                'lowercase',
                'not_regex:/\.{2,}/', // Keine mehrfachen Punkte
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ];

            // Nur ignore hinzufügen wenn contactId existiert (Update)
            if ($this->getContactId()) {
                $emailRule[] = Rule::unique('sos', 'email')->ignore($this->getContactId());
            } else {
                $emailRule[] = Rule::unique('sos', 'email');
            }

            $rules['email'] = $emailRule;
        }

        // Phone 1 - Starke Validierung
        if ($this->phoneHasChanged()) {
            $rules['phone'] = [
                'required',
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
        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        $emailRule = [
            'nullable',
            'string',
            'email:rfc,dns,spoof,filter',
            'max:255',
            'lowercase',
            'not_regex:/\.{2,}/',
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        ];

        // Nur ignore hinzufügen wenn contactId existiert (Update)
        if ($this->getContactId()) {
            $emailRule[] = Rule::unique('sos', 'email')->ignore($this->getContactId());
        } else {
            $emailRule[] = Rule::unique('sos', 'email');
        }

        return [
            'gender' => ['nullable', Rule::enum(Gender::class)],
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
            'related' => [
                'nullable',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZäöüÄÖÜéèêëàâîïôùûçÇßñÑ\s\-\'\.]+$/u',
                'not_regex:/\d/',
                'not_regex:/[!@#$%^&*()_+=\[\]{};:"\\|,<>\/?]/',
                'not_regex:/\s{2,}/',
            ],
            'email' => $emailRule,
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\d\s\-\+\(\)\.]+$/',
                'not_regex:/[a-zA-Z]/',
            ],
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

            // Related
            'related.string' => __('Related person\'s name must be a string.'),
            'related.min' => __('Related person\'s name must be at least 2 characters.'),
            'related.max' => __('Related person\'s name must not exceed 255 characters.'),
            'related.regex' => __('Related person\'s name can only contain letters, spaces, hyphens, apostrophes and dots.'),
            'related.not_regex' => __('Related person\'s name contains invalid characters or formatting.'),

            // Gender
            'gender.enum' => __('The selected gender is invalid.'),

            // Email
            'email.string' => __('Email must be a string.'),
            'email.email' => __('Please provide a valid email address.'),
            'email.max' => __('Email address must not exceed 255 characters.'),
            'email.unique' => __('This email address is already registered in the system.'),
            'email.lowercase' => __('Email address must be in lowercase.'),
            'email.regex' => __('Email address format is invalid.'),
            'email.not_regex' => __('Email address contains invalid formatting.'),

            // Phone
            'phone.required' => __('Phone number is required.'),
            'phone.string' => __('Phone number must be a string.'),
            'phone.min' => __('Phone number must be at least 10 characters long.'),
            'phone.max' => __('Phone number must not exceed 20 characters.'),
            'phone.regex' => __('Phone number can only contain digits, spaces, hyphens, plus signs, parentheses and dots.'),
            'phone.not_regex' => __('Phone number cannot contain letters.'),
            'phone.digits_between' => __('Phone number must contain between 10 and 15 digits.'),
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

        if ($this->relatedHasChanged()) {
            $changed['related'] = [
                'old' => $this->originalData['related'] ?? null,
                'new' => $this->related
            ];
        }

        if ($this->emailHasChanged()) {
            $changed['email'] = [
                'old' => $this->originalData['email'] ?? null,
                'new' => $this->email
            ];
        }

        if ($this->phoneHasChanged()) {
            $changed['phone'] = [
                'old' => $this->originalData['phone'] ?? null,
                'new' => $this->phone
            ];
        }
        return $changed;
    }

    /**
     * Helper: Check ob Gender geändert wurde
     */
    private function genderHasChanged(): bool
    {
        // Für neue Einträge (CreateContact) existiert originalData nicht
        if (!isset($this->originalData)) {
            return true;
        }
        return $this->gender !== ($this->originalData['gender'] ?? null);
    }

    /**
     * Helper: Check ob Name geändert wurde
     */
    private function nameHasChanged(): bool
    {
        if (!isset($this->originalData)) {
            return true;
        }
        return $this->name !== ($this->originalData['name'] ?? null);
    }

    /**
     * Helper: Check ob Related geändert wurde
     */
    private function relatedHasChanged(): bool
    {
        if (!isset($this->originalData)) {
            return true;
        }
        return $this->related !== ($this->originalData['related'] ?? null);
    }

    /**
     * Helper: Check ob Email geändert wurde
     */
    private function emailHasChanged(): bool
    {
        if (!isset($this->originalData)) {
            return true;
        }
        return $this->email !== ($this->originalData['email'] ?? null);
    }

    /**
     * Helper: Check ob Phone geändert wurde
     */
    private function phoneHasChanged(): bool
    {
        if (!isset($this->originalData)) {
            return true;
        }
        return $this->phone !== ($this->originalData['phone'] ?? null);
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->genderHasChanged() ||
            $this->nameHasChanged() ||
            $this->relatedHasChanged() ||
            $this->emailHasChanged() ||
            $this->phoneHasChanged();
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

    /**
     * Helper Methode um Contact ID zu erhalten
     * Muss in der verwendenden Komponente implementiert werden
     */
    protected function getContactId(): ?int
    {
        // In ContactRow: return $this->contact->id;
        // In CreateContact: return null;
        if (property_exists($this, 'contact') && $this->contact && isset($this->contact->id)) {
            return $this->contact->id;
        }

        if (property_exists($this, 'contactId')) {
            return $this->contactId;
        }

        return null;
    }
}
